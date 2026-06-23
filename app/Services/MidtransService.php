<?php

namespace App\Services;

use App\Models\Booking;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    /**
     * Konfigurasi Midtrans global — dipanggil sekali sebelum transaksi.
     * PENTING: Server Key hanya digunakan di sisi backend.
     * Client Key dipakai oleh frontend Snap JS.
     */
    public static function configure(): void
    {
        \Midtrans\Config::$serverKey    = config('midtrans.server_key');
        \Midtrans\Config::$clientKey    = config('midtrans.client_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$isSanitized  = true;
        \Midtrans\Config::$is3ds        = config('midtrans.enable_3ds', true);

        // Override URL jika disediakan (biasanya untuk testing endpoint)
        if ($snapUrl = config('midtrans.snap_url')) {
            \Midtrans\Config::$snapUrl = $snapUrl;
        }
        if ($irisUrl = config('midtrans.iris_url')) {
            \Midtrans\Config::$irisUrl = $irisUrl;
        }
        if ($coreApiUrl = config('midtrans.core_api_url')) {
            \Midtrans\Config::$overrideNotifUrl = $coreApiUrl;
        }
    }

    /**
     * Buat Snap Token untuk membuka halaman pembayaran Midtrans.
     *
     * @param Booking $booking
     * @return string Snap token
     * @throws \Exception
     */
    public static function createSnapToken(Booking $booking): string
    {
        self::configure();

        // Setiap kali minta Snap token baru, gunakan order_id yang BENAR-BENAR BARU.
        // Midtrans tidak mengizinkan dua Snap token dengan order_id yang sama,
        // jadi jika user menutup popup lalu membuka lagi, kita harus pakai ID baru.
        // Format: BOOKING-{booking_id}-{timestamp}-{random}  (unik setiap panggilan)
        $booking->order_id = 'BOOKING-' . $booking->id . '-' . now()->timestamp . '-' . bin2hex(random_bytes(3));

        // Data transaksi untuk Midtrans Snap
        $transactionDetails = [
            'order_id'     => $booking->order_id,
            'gross_amount' => (int) round($booking->total_price), // Midtrans expects integer
        ];

        // Data customer yang membayar
        $customerDetails = [
            'first_name' => $booking->user->name,
            'email'      => $booking->user->email,
            'phone'      => $booking->user->whatsapp_number ?? '',
        ];

        // Data item (detail booking)
        $itemDetails = [
            [
                'id'       => 'CAR-' . $booking->car_id,
                'price'    => (int) round($booking->car->price_per_day),
                'quantity' => (int) $booking->total_days,
                'name'     => 'Sewa: ' . $booking->car->brand . ' ' . $booking->car->model .
                              ' (' . $booking->total_days . ' hari)',
            ],
        ];

        // Jika ada driver, tambahkan sebagai item terpisah
        // (untuk transparansi, meskipun driver already included in car price)
        if ($booking->driver_id) {
            $itemDetails[] = [
                'id'       => 'DRIVER-' . $booking->driver_id,
                'price'    => 0, // Biaya driver sudah termasuk
                'quantity' => (int) $booking->total_days,
                'name'     => 'Driver: ' . $booking->driver->name . ' (' . $booking->total_days . ' hari)',
            ];
        }

        $params = [
            'transaction_details' => $transactionDetails,
            'customer_details'    => $customerDetails,
            'item_details'        => $itemDetails,
            // Callbacks
            'callbacks' => [
                'finish' => route('customer.payment.finish', ['booking' => $booking->id]),
                'unfinish' => route('customer.payment.unfinish', ['booking' => $booking->id]),
                'error' => route('customer.payment.error', ['booking' => $booking->id]),
            ],
            // Waktu kadaluarsa transaksi (dalam menit) — ambil dari config atau default 30
            'expiry' => [
                'duration' => (int) config('business.payment_window_minutes', 30),
                'unit'     => 'minutes',
            ],
        ];

        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);

            // Simpan order_id & snap_token ke database
            $booking->update([
                'order_id'  => $booking->order_id,
                'snap_token' => $snapToken,
                'gross_amount' => $transactionDetails['gross_amount'],
            ]);

            Log::info('Midtrans Snap token created', [
                'booking_id' => $booking->id,
                'order_id'   => $booking->order_id,
            ]);

            return $snapToken;
        } catch (\Exception $e) {
            Log::error('Midtrans Snap token creation failed', [
                'booking_id' => $booking->id,
                'error'      => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Proses notifikasi webhook dari Midtrans.
     * Endpoint ini dipanggil Midtrans setiap kali status transaksi berubah.
     *
     * @param object|array $notification Notification object dari Midtrans API
     * @return array Hasil pemrosesan
     */
    public static function handleNotification($notification): array
    {
        self::configure();

        $notification = (object) $notification;

        $orderId        = $notification->order_id ?? null;
        $transactionStatus = $notification->transaction_status ?? null;
        $fraudStatus    = $notification->fraud_status ?? null;

        Log::info('Midtrans notification received', [
            'order_id'            => $orderId,
            'transaction_status'  => $transactionStatus,
            'fraud_status'        => $fraudStatus,
        ]);

        // Temukan booking berdasarkan order_id
        $booking = Booking::where('order_id', $orderId)->first();

        if (! $booking) {
            Log::warning('Midtrans notification: booking not found', ['order_id' => $orderId]);
            return ['success' => false, 'message' => 'Booking not found'];
        }

        // Simpan raw response untuk audit trail
        $booking->update([
            'midtrans_response' => json_encode($notification),
        ]);

        // Tentukan status booking & payment berdasarkan transaction_status dan fraud_status
        $newPaymentStatus = null;
        $newBookingStatus  = null;

        // Mapping status Midtrans → status internal
        // Ref: https://docs.midtrans.com/docs/transaction-status
        switch ($transactionStatus) {
            case 'capture':
                // Capture hanya untuk kartu kredit
                if ($fraudStatus === 'accept') {
                    $newPaymentStatus = 'paid';
                    $newBookingStatus  = 'confirmed';
                }
                break;

            case 'settlement':
                // Pembayaran sukses (berhasil diterima)
                $newPaymentStatus = 'paid';
                $newBookingStatus  = 'confirmed';
                break;

            case 'pending':
                // Pembayaran masih menunggu
                $newPaymentStatus = 'unpaid';
                $newBookingStatus  = 'pending';
                break;

            case 'deny':
                // Pembayaran ditolak
                $newPaymentStatus = 'unpaid';
                $newBookingStatus  = 'pending';
                break;

            case 'expire':
                // Transaksi kedaluwarsa tanpa pembayaran — batalkan booking
                $newPaymentStatus = 'unpaid';
                $newBookingStatus  = 'cancelled';
                break;

            case 'cancel':
                // Transaksi dibatalkan
                $newPaymentStatus = 'unpaid';
                $newBookingStatus  = 'cancelled';
                break;

            case 'refund':
            case 'partial_refund':
                // Refund — status booking perlu dicek manual oleh admin
                $newPaymentStatus = 'unpaid';
                $newBookingStatus  = 'cancelled';
                break;
        }

        if ($newPaymentStatus !== null || $newBookingStatus !== null) {
            $booking->update([
                'payment_type'       => $notification->payment_type ?? null,
                'payment_channel'    => $notification->bank ?? ($notification->payment_type ?? null),
                'transaction_status' => $transactionStatus,
                'transaction_time'   => $notification->transaction_time ?? null,
                'settlement_time'    => $notification->settlement_time ?? null,
                'gross_amount'       => $notification->gross_amount ?? $booking->gross_amount,
                'payment_status'     => $newPaymentStatus ?? $booking->payment_status,
                'status'             => $newBookingStatus ?? $booking->status,
            ]);

            Log::info('Midtrans notification: booking updated', [
                'booking_id'          => $booking->id,
                'order_id'            => $orderId,
                'payment_status'      => $newPaymentStatus,
                'booking_status'      => $newBookingStatus,
                'transaction_status'  => $transactionStatus,
            ]);
        }

        return ['success' => true, 'booking_id' => $booking->id];
    }

    /**
     * Verifikasi signature key dari notifikasi Midtrans.
     * Ini memastikan notifikasi benar-benar berasal dari Midtrans.
     *
     * @param string $orderId
     * @param string $statusCode
     * @param string $grossAmount
     * @param string $serverKey
     * @return string
     */
    public static function verifySignature(string $orderId, string $statusCode, string $grossAmount, string $serverKey): string
    {
        return hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
    }
}
