<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssignDriverRequest;
use App\Http\Requests\UpdateBookingStatusRequest;
use App\Models\Booking;
use App\Models\Driver;
use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BookingController extends Controller
{
    /**
     * Display a listing of bookings.
     */
    public function index(Request $request)
    {
        $query = Booking::with(['user', 'car', 'driver']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Search by booking ID or user name
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('id', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function($q2) use ($request) {
                      $q2->where('name', 'like', '%' . $request->search . '%');
                  });
            });
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.bookings.index', compact('bookings'));
    }

    /**
     * Display the specified booking.
     */
    public function show($id)
    {
        $booking = Booking::with(['user', 'car', 'driver'])->findOrFail($id);
        $availableDrivers = Driver::where('status', 'available')->with('user')->get();

        return view('admin.bookings.show', compact('booking', 'availableDrivers'));
    }

    /**
     * Update booking status.
     */
    public function updateStatus(UpdateBookingStatusRequest $request, $id)
    {
        $booking = Booking::findOrFail($id);
        $oldStatus = $booking->status;

        // State machine: transisi status yang diperbolehkan. 'completed' & 'cancelled'
        // bersifat final (daftar tujuan kosong). Mencegah lompatan tidak sah
        // seperti pending → ongoing (melewati pembayaran) atau pending → completed.
        $allowedTransitions = [
            'pending'   => ['confirmed', 'cancelled'],
            'confirmed' => ['ongoing', 'cancelled'],
            'ongoing'   => ['completed', 'cancelled'],
            'completed' => [],
            'cancelled' => [],
        ];

        if ($request->status !== $oldStatus
            && ! in_array($request->status, $allowedTransitions[$oldStatus] ?? [], true)) {
            return back()->with('error', "Transisi status dari '{$oldStatus}' ke '{$request->status}' tidak diperbolehkan.");
        }

        DB::transaction(function () use ($booking, $request, $oldStatus) {
            $booking->update(['status' => $request->status]);

            // Update status mobil & driver mengikuti status booking
            if ($request->status === 'completed' || $request->status === 'cancelled') {
                // Bebaskan mobil HANYA jika tidak ada booking lain yang masih
                // benar-benar memakainya (status 'ongoing'). Booking masa depan
                // (confirmed) tidak boleh menahan mobil sebagai 'rented'.
                $carStillInUse = Booking::where('car_id', $booking->car_id)
                    ->where('id', '!=', $booking->id)
                    ->where('status', 'ongoing')
                    ->exists();
                if (! $carStillInUse) {
                    $booking->car->update(['status' => 'available']);
                }

                // Bebaskan driver HANYA jika tidak sedang bertugas di booking lain.
                if ($booking->driver_id) {
                    $driverStillOnDuty = Booking::where('driver_id', $booking->driver_id)
                        ->where('id', '!=', $booking->id)
                        ->where('status', 'ongoing')
                        ->exists();
                    if (! $driverStillOnDuty) {
                        Driver::where('user_id', $booking->driver_id)
                            ->update(['status' => 'available']);
                    }
                }
            } elseif ($request->status === 'ongoing' && $oldStatus !== 'ongoing') {
                $booking->car->update(['status' => 'rented']);

                // Set driver ke 'on_duty' jika ada driver yang ditugaskan
                if ($booking->driver_id) {
                    Driver::where('user_id', $booking->driver_id)
                        ->update(['status' => 'on_duty']);
                }
            }
        });

        return back()->with('success', 'Status booking berhasil diupdate');
    }

    /**
     * Assign driver to booking.
     */
    public function assignDriver(AssignDriverRequest $request, $id)
    {
        $booking = Booking::findOrFail($id);

        // Tidak bisa menugaskan driver ke booking yang sudah selesai/dibatalkan.
        if (in_array($booking->status, ['completed', 'cancelled'])) {
            return back()->with('error', 'Tidak dapat menugaskan driver pada booking yang sudah selesai atau dibatalkan');
        }

        // Seluruh cek-overlap + penugasan dibungkus transaksi dengan lockForUpdate
        // untuk mencegah race condition (dua admin menugaskan driver yang sama ke
        // dua booking bertumpang-waktu secara bersamaan / TOCTOU).
        try {
            DB::transaction(function () use ($booking, $request) {
                // Kunci baris booking driver terkait agar request lain menunggu.
                $pickup = $booking->start_date->format('Y-m-d') . ' ' . ($booking->pickup_time ?? '00:00:00');
                $return = $booking->end_date->format('Y-m-d') . ' ' . ($booking->return_time ?? '00:00:00');

                $driverBusy = Booking::where('driver_id', $request->driver_id)
                    ->where('id', '!=', $booking->id)
                    ->blockingSlot()
                    ->whereRaw("CONCAT(start_date, ' ', COALESCE(pickup_time, '00:00:00')) < ?", [$return])
                    ->whereRaw("CONCAT(end_date, ' ', COALESCE(return_time, '00:00:00')) > ?", [$pickup])
                    ->lockForUpdate()
                    ->exists();

                if ($driverBusy) {
                    throw new \RuntimeException('Driver tersebut sudah memiliki tugas pada rentang tanggal booking ini. Silakan pilih driver lain.');
                }

                // Bebaskan driver lama jika ada
                if ($booking->driver_id) {
                    Driver::where('user_id', $booking->driver_id)->update(['status' => 'available']);
                }

                // Tugaskan driver baru (status tetap 'available' sampai booking benar-benar dimulai)
                $booking->update(['driver_id' => $request->driver_id]);
            });
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Driver berhasil ditugaskan');
    }

    /**
     * Verify payment.
     */
    public function verifyPayment($id)
    {
        $booking = Booking::findOrFail($id);

        if (!$booking->payment_proof) {
            return back()->with('error', 'Belum ada bukti pembayaran yang diupload');
        }

        $booking->update([
            'payment_status' => 'paid',
            'status' => 'confirmed', // Auto confirm when payment is verified
        ]);

        // CATATAN: Status mobil dan driver TIDAK diubah di sini.
        // Mobil tetap 'available' sampai booking berubah ke 'ongoing' (saat admin update status atau driver mulai tugas).
        // Hal ini mencegah mobil terkunci sebagai 'rented' padahal belum digunakan.

        return back()->with('success', 'Pembayaran berhasil diverifikasi dan booking dikonfirmasi');
    }

    /**
     * Reject payment.
     */
    public function rejectPayment(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        // Hapus berkas bukti bayar (PII) dari disk privat 'local'; cek 'public' untuk berkas legacy.
        if ($booking->payment_proof) {
            Storage::disk('local')->delete($booking->payment_proof);
            Storage::disk('public')->delete($booking->payment_proof);
        }

        $booking->update([
            'payment_proof' => null,
            'payment_status' => 'unpaid',
        ]);

        return back()->with('success', 'Bukti pembayaran ditolak. Customer harus upload ulang');
    }
}
