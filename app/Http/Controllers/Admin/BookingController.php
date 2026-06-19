<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Driver;
use App\Models\Car;
use Illuminate\Http\Request;
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
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,ongoing,completed,cancelled',
        ]);

        $booking = Booking::findOrFail($id);
        $oldStatus = $booking->status;

        // Booking yang sudah selesai/dibatalkan bersifat final — tidak boleh diubah lagi.
        if (in_array($oldStatus, ['completed', 'cancelled']) && $request->status !== $oldStatus) {
            return back()->with('error', 'Status booking yang sudah selesai atau dibatalkan tidak dapat diubah lagi');
        }

        $booking->update(['status' => $request->status]);

        // Update car status based on booking status
        if ($request->status === 'completed' || $request->status === 'cancelled') {
            $booking->car->update(['status' => 'available']);
            
            // Update driver status if there's a driver
            if ($booking->driver_id) {
                $driver = Driver::where('user_id', $booking->driver_id)->first();
                if ($driver) {
                    $driver->update(['status' => 'available']);
                }
            }
        } elseif ($request->status === 'ongoing' && $oldStatus !== 'ongoing') {
            $booking->car->update(['status' => 'rented']);
            
            // Set driver ke 'on_duty' jika ada driver yang ditugaskan
            if ($booking->driver_id) {
                $driver = Driver::where('user_id', $booking->driver_id)->first();
                if ($driver) {
                    $driver->update(['status' => 'on_duty']);
                }
            }
        }

        return back()->with('success', 'Status booking berhasil diupdate');
    }

    /**
     * Assign driver to booking.
     */
    public function assignDriver(Request $request, $id)
    {
        $request->validate([
            'driver_id' => 'required|exists:drivers,user_id',
        ]);

        $booking = Booking::findOrFail($id);

        // Tidak bisa menugaskan driver ke booking yang sudah selesai/dibatalkan.
        if (in_array($booking->status, ['completed', 'cancelled'])) {
            return back()->with('error', 'Tidak dapat menugaskan driver pada booking yang sudah selesai atau dibatalkan');
        }

        // Release old driver if exists
        if ($booking->driver_id) {
            $oldDriver = Driver::where('user_id', $booking->driver_id)->first();
            if ($oldDriver) {
                $oldDriver->update(['status' => 'available']);
            }
        }

        // Assign new driver (status tetap 'available' sampai booking benar-benar dimulai)
        $booking->update(['driver_id' => $request->driver_id]);

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

        // Delete payment proof file
        if ($booking->payment_proof) {
            Storage::disk('public')->delete($booking->payment_proof);
        }

        $booking->update([
            'payment_proof' => null,
            'payment_status' => 'unpaid',
        ]);

        return back()->with('success', 'Bukti pembayaran ditolak. Customer harus upload ulang');
    }
}
