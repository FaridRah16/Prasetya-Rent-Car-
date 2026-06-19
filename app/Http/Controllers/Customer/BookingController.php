<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Car;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
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
            'pickup_time' => 'required|date_format:H:i',
            'end_date' => 'required|date|after_or_equal:start_date',
            'return_time' => 'required|date_format:H:i',
            'pickup_location' => 'required|string|max:255',
            'dropoff_location' => 'required|string|max:255',
            'pickup_lat' => 'nullable|numeric',
            'pickup_lng' => 'nullable|numeric',
            'dropoff_lat' => 'nullable|numeric',
            'dropoff_lng' => 'nullable|numeric',
            'driver_id' => 'nullable|exists:drivers,user_id',
            'notes' => 'nullable|string',
        ], [
            'car_id.required' => 'Pilih mobil yang ingin disewa',
            'car_id.exists' => 'Mobil tidak ditemukan',
            'start_date.required' => 'Tanggal mulai harus diisi',
            'start_date.after_or_equal' => 'Tanggal mulai minimal hari ini',
            'pickup_time.required' => 'Jam penjemputan harus diisi',
            'pickup_time.date_format' => 'Format jam penjemputan tidak valid',
            'end_date.required' => 'Tanggal selesai harus diisi',
            'end_date.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai',
            'return_time.required' => 'Jam pengembalian harus diisi',
            'return_time.date_format' => 'Format jam pengembalian tidak valid',
            'pickup_location.required' => 'Lokasi penjemputan harus diisi',
            'dropoff_location.required' => 'Lokasi pengantaran harus diisi',
            'driver_id.exists' => 'Driver tidak ditemukan',
        ]);

        // Calculate pickup & return datetime (gabungan tanggal + jam)
        $pickupDateTime = Carbon::parse($request->start_date . ' ' . $request->pickup_time);
        $returnDateTime = Carbon::parse($request->end_date . ' ' . $request->return_time);

        // Validate: return must be after pickup
        if ($returnDateTime->lte($pickupDateTime)) {
            return back()->withErrors(['return_time' => 'Waktu pengembalian harus setelah waktu penjemputan'])
                ->withInput();
        }

        // Hitung jumlah hari berdasarkan jam (pembulatan ke atas)
        // Contoh: 16 10:00 → 17 10:00 = 24 jam = 1 hari
        // Contoh: 16 10:00 → 17 14:00 = 28 jam = 2 hari
        $totalHours = $pickupDateTime->diffInHours($returnDateTime);
        $totalDays = (int) ceil($totalHours / 24);
        if ($totalDays < 1) $totalDays = 1;

        // Dibungkus transaction + lockForUpdate untuk mencegah race condition
        // (dua request bersamaan lolos cek overlap dan membuat double-booking).
        $booking = DB::transaction(function () use ($request, $pickupDateTime, $returnDateTime, $totalDays) {
            // Lock baris mobil agar concurrent request menunggu
            $car = Car::lockForUpdate()->findOrFail($request->car_id);

            // Cek ulang ketersediaan mobil di dalam lock
            if ($car->status !== 'available') {
                throw ValidationException::withMessages([
                    'car_id' => 'Mobil tidak tersedia untuk disewa',
                ]);
            }

            // Cek booking yang tumpang-tindih, mempertimbangkan TANGGAL + JAM.
            // Dua rentang waktu [A_start, A_end) dan [B_start, B_end) overlap jika:
            //   A_start < B_end  AND  B_start < A_end
            // Disimpan sebagai DATETIME string (Y-m-d H:i:s) agar perbandingan akurat per jam.
            $overlapping = Booking::where('car_id', $request->car_id)
                ->whereIn('status', ['pending', 'confirmed', 'ongoing'])
                ->where(function ($query) use ($pickupDateTime, $returnDateTime) {
                    // Bangun datetime dari kolom tanggal + jam (fallback jam 00:00 jika NULL)
                    $query->whereRaw(
                        "CONCAT(start_date, ' ', COALESCE(pickup_time, '00:00:00')) < ?",
                        [$returnDateTime->format('Y-m-d H:i:s')]
                    )
                    ->whereRaw(
                        "CONCAT(end_date, ' ', COALESCE(return_time, '00:00:00')) > ?",
                        [$pickupDateTime->format('Y-m-d H:i:s')]
                    );
                })
                ->lockForUpdate()
                ->exists();

            if ($overlapping) {
                throw ValidationException::withMessages([
                    'car_id' => 'Mobil sudah di-booking pada tanggal & jam yang Anda pilih. Silakan pilih jadwal lain.',
                ]);
            }

            // Cek tumpang-tindih DRIVER (jika customer memilih driver).
            // Mencegah satu driver ditugaskan ke dua booking pada rentang waktu yang sama.
            if ($request->driver_id) {
                $driverOverlap = Booking::where('driver_id', $request->driver_id)
                    ->whereIn('status', ['pending', 'confirmed', 'ongoing'])
                    ->where(function ($query) use ($pickupDateTime, $returnDateTime) {
                        $query->whereRaw(
                            "CONCAT(start_date, ' ', COALESCE(pickup_time, '00:00:00')) < ?",
                            [$returnDateTime->format('Y-m-d H:i:s')]
                        )
                        ->whereRaw(
                            "CONCAT(end_date, ' ', COALESCE(return_time, '00:00:00')) > ?",
                            [$pickupDateTime->format('Y-m-d H:i:s')]
                        );
                    })
                    ->lockForUpdate()
                    ->exists();

                if ($driverOverlap) {
                    throw ValidationException::withMessages([
                        'driver_id' => 'Driver sudah ditugaskan pada tanggal & jam yang Anda pilih. Silakan pilih driver lain atau jadwal lain.',
                    ]);
                }
            }

            $totalPrice = $totalDays * $car->price_per_day;

            // Create booking
            return Booking::create([
                'user_id' => Auth::id(),
                'car_id' => $request->car_id,
                'driver_id' => $request->driver_id,
                'start_date' => $request->start_date,
                'pickup_time' => $request->pickup_time,
                'end_date' => $request->end_date,
                'return_time' => $request->return_time,
                'total_days' => $totalDays,
                'total_price' => $totalPrice,
                'pickup_location' => $request->pickup_location,
                'pickup_lat' => $request->pickup_lat,
                'pickup_lng' => $request->pickup_lng,
                'dropoff_location' => $request->dropoff_location,
                'dropoff_lat' => $request->dropoff_lat,
                'dropoff_lng' => $request->dropoff_lng,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'notes' => $request->notes,
            ]);
        });

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

        // Booking yang masih 'pending' belum pernah mengubah status mobil/driver
        // (perubahan ke 'rented'/'on_duty' baru terjadi saat status menjadi 'ongoing').
        // Karena itu pembatalan cukup mengubah status booking ini saja, tanpa menyentuh
        // status mobil/driver — agar tidak menimpa state milik booking lain yang sedang aktif
        // (mis. mobil yang sama sedang 'rented' oleh booking ongoing yang berbeda).
        $booking->update(['status' => 'cancelled']);

        return redirect()->route('customer.bookings.index')
            ->with('success', 'Booking berhasil dibatalkan');
    }
}
