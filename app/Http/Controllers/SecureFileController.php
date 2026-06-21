<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Menyajikan berkas PII (foto SIM, bukti bayar, bukti pengantaran) lewat
 * route ber-otorisasi, bukan dari /storage publik yang bisa diakses siapa pun.
 */
class SecureFileController extends Controller
{
    /**
     * Foto SIM: hanya admin atau pemilik akun.
     */
    public function sim(string $id): StreamedResponse
    {
        $user = User::findOrFail($id);

        abort_unless(Auth::user()->isAdmin() || Auth::id() === $user->id, 403);

        return $this->serve($user->sim_photo);
    }

    /**
     * Bukti pembayaran: hanya admin atau pemilik booking.
     */
    public function payment(string $id): StreamedResponse
    {
        $booking = Booking::findOrFail($id);

        abort_unless(Auth::user()->isAdmin() || Auth::id() === $booking->user_id, 403);

        return $this->serve($booking->payment_proof);
    }

    /**
     * Bukti pengantaran: admin, pemilik booking, atau driver yang ditugaskan.
     */
    public function delivery(string $id): StreamedResponse
    {
        $booking = Booking::findOrFail($id);

        abort_unless(
            Auth::user()->isAdmin()
                || Auth::id() === $booking->user_id
                || Auth::id() === $booking->driver_id,
            403
        );

        return $this->serve($booking->delivery_proof);
    }

    /**
     * Stream berkas dari disk privat 'local'. Fallback ke 'public' untuk
     * berkas lama yang diunggah sebelum migrasi ke disk privat.
     */
    private function serve(?string $path): StreamedResponse
    {
        abort_if(! $path, 404);

        foreach (['local', 'public'] as $disk) {
            if (Storage::disk($disk)->exists($path)) {
                return Storage::disk($disk)->response($path);
            }
        }

        abort(404);
    }
}
