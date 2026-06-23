<?php

namespace App\Http\Controllers;

use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentNotificationController extends Controller
{
    /**
     * Webhook untuk menerima notifikasi dari Midtrans.
     * Endpoint ini DIPANGGIL OLEH MIDTRANS (server-to-server),
     * BUKAN oleh customer. Karena itu:
     * - Route ini dikecualikan dari CSRF verification
     * - Route ini TIDAK pakai middleware 'auth'
     *
     * POST /api/payment/notification
     */
    public function handle(Request $request)
    {
        // Validasi signature key untuk memastikan request benar dari Midtrans
        if (! $this->isValidSignature($request)) {
            Log::warning('Midtrans notification: invalid signature', [
                'ip'          => $request->ip(),
                'order_id'    => $request->input('order_id'),
            ]);

            return response()->json([
                'status_code' => 400,
                'status_message' => 'Invalid signature',
            ], 400);
        }

        try {
            $result = MidtransService::handleNotification($request->all());

            if ($result['success']) {
                return response()->json([
                    'status_code'    => 200,
                    'status_message' => 'OK',
                ]);
            }

            return response()->json([
                'status_code'    => 404,
                'status_message' => 'Booking not found',
            ], 404);
        } catch (\Exception $e) {
            Log::error('Midtrans notification: unhandled error', [
                'order_id' => $request->input('order_id'),
                'error'    => $e->getMessage(),
            ]);

            return response()->json([
                'status_code'    => 500,
                'status_message' => 'Internal server error',
            ], 500);
        }
    }

    /**
     * Validasi signature key dari notifikasi Midtrans.
     *
     * Rumus: SHA512(order_id + status_code + gross_amount + server_key)
     *
     * @param Request $request
     * @return bool
     */
    private function isValidSignature(Request $request): bool
    {
        $orderId    = $request->input('order_id') ?? '';
        $statusCode = (string) ($request->input('status_code') ?? '');
        $grossAmount = (string) ($request->input('gross_amount') ?? '');
        $serverKey  = config('midtrans.server_key');

        $expectedSignature = MidtransService::verifySignature(
            $orderId, $statusCode, $grossAmount, $serverKey
        );

        $receivedSignature = $request->input('signature_key') ?? '';

        return hash_equals($expectedSignature, $receivedSignature);
    }
}
