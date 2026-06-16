<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Car;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * Display a listing of bookings.
     */
    public function index()
    {
        $bookings = Booking::where('user_id', Auth::id())
            ->with(['car', 'driver'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('customer.bookings.index', compact('bookings'));
    }

    /**
     * Show the form for creating a new booking.
     */
    public function create(Request $request)
    {
        $selectedCarId = $request->input('car_id');
        
        // Get available cars
        $cars = Car::where('status', 'available')->get();
        
        // Get available drivers
        $drivers = Driver::where('status', 'available')
            ->with('user')
            ->get();

        return view('customer.bookings.create', compact('cars', 'drivers', 'selectedCarId'));
    }

    /**
     * Store a newly created booking.
     */
    public function store(Request $request)
    {
        $request->validate([
            'car_id' => 'required|exists:cars,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'pickup_location' => 'required|string|max:255',
            'dropoff_location' => 'required|string|max:255',
            'driver_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ], [
            'car_id.required' => 'Pilih mobil yang ingin disewa',
            'car_id.exists' => 'Mobil tidak ditemukan',
            'start_date.required' => 'Tanggal mulai harus diisi',
            'start_date.after_or_equal' => 'Tanggal mulai minimal hari ini',
            'end_date.required' => 'Tanggal selesai harus diisi',
            'end_date.after' => 'Tanggal selesai harus setelah tanggal mulai',
            'pickup_location.required' => 'Lokasi penjemputan harus diisi',
            'dropoff_location.required' => 'Lokasi pengantaran harus diisi',
            'driver_id.exists' => 'Driver tidak ditemukan',
        ]);

        // Get car
        $car = Car::findOrFail($request->car_id);

        // Check if car is available
        if ($car->status !== 'available') {
            return back()->with('error', 'Mobil tidak tersedia untuk disewa');
        }

        // Cek apakah ada booking yang tumpang-tindih untuk mobil yang sama di tanggal tersebut
        $overlappingBooking = Booking::where('car_id', $request->car_id)
            ->whereIn('status', ['pending', 'confirmed', 'ongoing'])
            ->where(function($query) use ($request) {
                $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                    ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                    ->orWhere(function($q) use ($request) {
                        $q->where('start_date', '<=', $request->start_date)
                          ->where('end_date', '>=', $request->end_date);
                    });
            })
            ->exists();

        if ($overlappingBooking) {
            return back()->with('error', 'Mobil sudah di-booking pada tanggal yang Anda pilih. Silakan pilih tanggal lain.')
                ->withInput();
        }

        // Calculate total days and price
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $totalDays = (int) $startDate->diffInDays($endDate) + 1; // Include the start day
        $totalPrice = $totalDays * $car->price_per_day;

        // Create booking
        $booking = Booking::create([
            'user_id' => Auth::id(),
            'car_id' => $request->car_id,
            'driver_id' => $request->driver_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'total_days' => $totalDays,
            'total_price' => $totalPrice,
            'pickup_location' => $request->pickup_location,
            'dropoff_location' => $request->dropoff_location,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'notes' => $request->notes,
        ]);

        // Note: Car and driver status will be updated by admin after payment verification

        return redirect()->route('customer.bookings.show', $booking->id)
            ->with('success', 'Booking berhasil dibuat! Silakan lakukan pembayaran dan upload bukti bayar.');
    }

    /**
     * Display the specified booking.
     */
    public function show($id)
    {
        $booking = Booking::where('user_id', Auth::id())
            ->with(['car', 'driver'])
            ->findOrFail($id);

        return view('customer.bookings.show', compact('booking'));
    }

    /**
     * Upload payment proof.
     */
    public function uploadPayment(Request $request, $id)
    {
        $request->validate([
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'payment_proof.required' => 'File bukti pembayaran harus diupload',
            'payment_proof.image' => 'File harus berupa gambar',
            'payment_proof.mimes' => 'Format file harus jpeg, png, atau jpg',
            'payment_proof.max' => 'Ukuran file maksimal 2MB',
        ]);

        $booking = Booking::where('user_id', Auth::id())->findOrFail($id);

        if ($request->hasFile('payment_proof')) {
            // Delete old payment proof if exists
            if ($booking->payment_proof) {
                Storage::disk('public')->delete($booking->payment_proof);
            }

            // Store new payment proof
            $file = $request->file('payment_proof');
            $filename = 'payment_' . $booking->id . '_' . time() . '.' . $file->extension();
            $path = $file->storeAs('payments', $filename, 'public');

            // Update booking
            $booking->update([
                'payment_proof' => $path,
            ]);

            return back()->with('success', 'Bukti pembayaran berhasil diupload. Menunggu verifikasi admin.');
        }

        return back()->with('error', 'Gagal mengupload bukti pembayaran');
    }

    /**
     * Cancel booking.
     */
    public function cancel($id)
    {
        $booking = Booking::where('user_id', Auth::id())->findOrFail($id);

        // Only pending bookings can be cancelled
        if ($booking->status !== 'pending') {
            return back()->with('error', 'Hanya booking dengan status pending yang bisa dibatalkan');
        }

        // Cegah pembatalan jika mobil sudah berstatus 'rented' (sedang digunakan)
        if ($booking->car->status === 'rented') {
            return back()->with('error', 'Tidak dapat membatalkan booking karena mobil sudah dalam proses penggunaan');
        }

        // Update booking status
        $booking->update(['status' => 'cancelled']);

        // Update car status back to available (hanya jika mobil belum rented)
        $booking->car->update(['status' => 'available']);

        // Update driver status if there's a driver
        if ($booking->driver_id) {
            $driver = Driver::where('user_id', $booking->driver_id)->first();
            if ($driver) {
                $driver->update(['status' => 'available']);
            }
        }

        return redirect()->route('customer.bookings.index')
            ->with('success', 'Booking berhasil dibatalkan');
    }
}
