<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * Halaman pembayaran — menampilkan Midtrans Snap popup untuk booking.
     *
     * GET /customer/bookings/{booking}/payment
     */
    public function show($bookingId)
    {
        $booking = Booking::where('user_id', Auth::id())
            ->with(['car', 'driver'])
            ->findOrFail($bookingId);

        // Cek status booking
        if ($booking->status !== 'pending') {
            return redirect()->route('customer.bookings.show', $booking->id)
                ->with('error', 'Booking ini sudah tidak menunggu pembayaran.');
        }

        // Cek apakah sudah dibayar
        if ($booking->payment_status === 'paid') {
            return redirect()->route('customer.bookings.show', $booking->id)
                ->with('success', 'Pembayaran sudah selesai!');
        }

        // Lazy-expire: cek apakah batas waktu pembayaran sudah habis
        if ($booking->isPaymentExpired()) {
            $booking->update(['status' => 'cancelled']);
            $minutes = (int) config('business.payment_window_minutes', 30);
            return redirect()->route('customer.bookings.show', $booking->id)
                ->with('error', "Batas waktu pembayaran ({$minutes} menit) telah habis. Booking dibatalkan otomatis.");
        }

        // Generate Snap token BARU setiap kali halaman payment dibuka.
        try {
            $snapToken = MidtransService::createSnapToken($booking);
        } catch (\Exception $e) {
            Log::error('Gagal membuat Snap token di halaman payment', [
                'booking_id' => $booking->id,
                'error'      => $e->getMessage(),
            ]);
            return back()->with('error', 'Gagal membuka halaman pembayaran: ' . $e->getMessage());
        }

        // URL halaman pembayaran Midtrans Snap (VT-Web)
        // Redirect langsung ke sini — tidak pakai JavaScript Snap library.
        // Lebih stabil di mobile, tidak ada masalah popup/cross-origin.
        $snapBaseUrl = config('midtrans.is_production')
            ? 'https://app.midtrans.com/snap/v2/vtweb/'
            : 'https://app.sandbox.midtrans.com/snap/v2/vtweb/';

        return view('customer.payment.show', [
            'booking'      => $booking,
            'snapToken'    => $snapToken,
            'snapUrl'      => $snapBaseUrl . $snapToken,
        ]);
    }

    /**
     * Generate ulang Snap token (via AJAX) jika token lama sudah kadaluarsa.
     *
     * POST /customer/bookings/{booking}/payment/token
     */
    public function regenerateToken($bookingId)
    {
        $booking = Booking::where('user_id', Auth::id())->findOrFail($bookingId);

        if ($booking->status !== 'pending' || $booking->payment_status !== 'unpaid') {
            return response()->json(['error' => 'Booking tidak dalam status menunggu pembayaran.'], 422);
        }

        if ($booking->isPaymentExpired()) {
            $booking->update(['status' => 'cancelled']);
            return response()->json(['error' => 'Batas waktu pembayaran telah habis.'], 410);
        }

        try {
            $snapToken = MidtransService::createSnapToken($booking);
            return response()->json([
                'snap_token' => $snapToken,
                'order_id'   => $booking->order_id,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Callback setelah pembayaran SELESAI (dari Midtrans Snap redirect).
     * SELALU verifikasi status langsung ke Midtrans API — jangan percaya
     * query params saja (karena webhook Midtrans tidak bisa mencapai localhost).
     *
     * GET /customer/bookings/{booking}/payment/finish
     */
    public function finish(Request $request, $bookingId)
    {
        $booking = Booking::where('user_id', Auth::id())->findOrFail($bookingId);

        // Verifikasi status TERBARU langsung dari Midtrans API
        $result = $this->syncStatusFromMidtrans($booking);

        Log::info('Payment finish callback', [
            'booking_id'     => $booking->id,
            'order_id'       => $booking->order_id,
            'query_params'   => $request->only(['order_id', 'transaction_status', 'status_code']),
            'sync_result'    => $result,
        ]);

        // Refresh booking dari database setelah sync
        $booking->refresh();

        if ($booking->payment_status === 'paid' && $booking->status === 'confirmed') {
            return redirect()->route('customer.bookings.show', $booking->id)
                ->with('success', '✅ Pembayaran berhasil! Booking Anda telah dikonfirmasi.');
        }

        if ($booking->payment_status === 'paid') {
            return redirect()->route('customer.bookings.show', $booking->id)
                ->with('success', '✅ Pembayaran berhasil! Menunggu konfirmasi admin.');
        }

        if ($booking->status === 'cancelled') {
            return redirect()->route('customer.bookings.show', $booking->id)
                ->with('error', '❌ Pembayaran telah kadaluarsa/dibatalkan. Silakan buat booking baru.');
        }

        // Status masih pending — pembayaran mungkin masih diproses
        return redirect()->route('customer.bookings.show', $booking->id)
            ->with('warning', '⏳ Pembayaran Anda masih diproses oleh Midtrans. Klik "Cek Status" untuk memeriksa ulang.');
    }

    /**
     * Callback setelah pembayaran BELUM SELESAI.
     * Tetap cek ke Midtrans API siapa tahu payment sudah settlement.
     *
     * GET /customer/bookings/{booking}/payment/unfinish
     */
    public function unfinish(Request $request, $bookingId)
    {
        $booking = Booking::where('user_id', Auth::id())->findOrFail($bookingId);

        // Cek status ke Midtrans — mungkin sudah dibayar meskipun kena unfinish
        $this->syncStatusFromMidtrans($booking);
        $booking->refresh();

        if ($booking->payment_status === 'paid') {
            return redirect()->route('customer.bookings.show', $booking->id)
                ->with('success', '✅ Pembayaran berhasil terdeteksi! Booking Anda telah dikonfirmasi.');
        }

        return redirect()->route('customer.bookings.show', $booking->id)
            ->with('warning', '⏳ Pembayaran belum selesai. Silakan selesaikan pembayaran Anda, lalu klik "Cek Status".');
    }

    /**
     * Callback setelah pembayaran ERROR/GAGAL.
     * Cek ke Midtrans API untuk status sebenarnya.
     *
     * GET /customer/bookings/{booking}/payment/error
     */
    public function error(Request $request, $bookingId)
    {
        $booking = Booking::where('user_id', Auth::id())->findOrFail($bookingId);

        // Cek ke Midtrans siapa tahu transaksi sebenarnya sukses
        $this->syncStatusFromMidtrans($booking);
        $booking->refresh();

        if ($booking->payment_status === 'paid') {
            return redirect()->route('customer.bookings.show', $booking->id)
                ->with('success', '✅ Pembayaran berhasil! Meskipun ada kendala sebelumnya, transaksi Anda sukses.');
        }

        $statusCode = $request->get('status_code', 'Tidak diketahui');

        return redirect()->route('customer.bookings.show', $booking->id)
            ->with('error', "❌ Pembayaran gagal (kode: {$statusCode}). Silakan coba bayar lagi.");
    }

    /**
     * Cek status pembayaran manual — user klik tombol "Cek Status".
     * Berguna karena webhook Midtrans tidak bisa mencapai localhost.
     *
     * POST /customer/bookings/{booking}/payment/check-status
     */
    public function checkStatus($bookingId)
    {
        $booking = Booking::where('user_id', Auth::id())->findOrFail($bookingId);

        if (! $booking->order_id) {
            return back()->with('error', 'Belum ada transaksi pembayaran untuk booking ini.');
        }

        $result = $this->syncStatusFromMidtrans($booking);
        $booking->refresh();

        if ($result['updated']) {
            if ($booking->payment_status === 'paid') {
                return back()->with('success', '✅ Pembayaran terkonfirmasi! Booking Anda aktif.');
            }
            if ($booking->status === 'cancelled') {
                return back()->with('error', '❌ Pembayaran telah kadaluarsa/dibatalkan oleh sistem.');
            }
        }

        // Status dari Midtrans
        $status = $booking->transaction_status ?? 'unknown';
        $statusLabel = match ($status) {
            'settlement', 'capture' => 'BERHASIL — klik "Cek Status" untuk sinkronisasi',
            'pending'               => 'MENUNGGU PEMBAYARAN — silakan selesaikan pembayaran Anda di Midtrans',
            'expire'                => 'KADALUARSA — waktu pembayaran telah habis',
            'cancel'                => 'DIBATALKAN',
            'deny'                  => 'DITOLAK — silakan coba metode pembayaran lain',
            default                 => strtoupper($status),
        };

        $type = match ($status) {
            'settlement', 'capture' => 'warning',
            'pending'               => 'warning',
            'expire', 'cancel', 'deny' => 'error',
            default                 => 'info',
        };

        return back()->with($type, "Status Midtrans: {$statusLabel}.");
    }

    /**
     * Sinkronisasi status booking dengan Midtrans API.
     * Ini adalah pengganti webhook untuk development local (localhost).
     *
     * @param Booking $booking
     * @return array ['updated' => bool, 'status' => string]
     */
    private function syncStatusFromMidtrans(Booking $booking): array
    {
        if (! $booking->order_id) {
            return ['updated' => false, 'status' => 'no_order_id'];
        }

        try {
            MidtransService::configure();

            $response = \Midtrans\Transaction::status($booking->order_id);

            // Gunakan handleNotification yang sama dengan webhook
            $notification = array_merge((array) $response, [
                'order_id' => $booking->order_id,
            ]);

            $result = MidtransService::handleNotification($notification);

            Log::info('Payment status synced from Midtrans API', [
                'booking_id'  => $booking->id,
                'order_id'    => $booking->order_id,
                'status'      => $response->transaction_status ?? 'unknown',
                'sync_result' => $result,
            ]);

            return [
                'updated' => $result['success'] ?? false,
                'status'  => $response->transaction_status ?? 'unknown',
            ];
        } catch (\Exception $e) {
            Log::error('Gagal sinkronisasi status dari Midtrans API', [
                'booking_id' => $booking->id,
                'order_id'   => $booking->order_id,
                'error'      => $e->getMessage(),
            ]);

            return ['updated' => false, 'status' => 'error', 'error' => $e->getMessage()];
        }
    }
}
