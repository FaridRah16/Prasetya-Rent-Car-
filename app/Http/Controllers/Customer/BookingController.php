<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UploadPaymentRequest;
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
        // Wajib terverifikasi sebelum memesan
        if (! Auth::user()->isVerified()) {
            return redirect()->route('customer.profile.edit')
                ->with('error', 'Selesaikan verifikasi akun (nomor telepon & foto SIM) dan tunggu konfirmasi admin sebelum melakukan pemesanan.');
        }

        $selectedCarId = $request->input('car_id');
        
        // Mobil yang bisa dipesan: tersedia & sedang disewa (untuk tanggal lain).
        // Mobil 'maintenance' dikecualikan. Bentrok tanggal divalidasi saat store().
        $cars = Car::whereIn('status', ['available', 'rented'])->get();
        
        // Tampilkan semua driver; ketersediaan untuk tanggal yang dipilih
        // divalidasi saat store() lewat pengecekan bentrok jadwal (overlap).
        $drivers = Driver::with('user')->get();

        return view('customer.bookings.create', compact('cars', 'drivers', 'selectedCarId'));
    }

    /**
     * Store a newly created booking.
     */
    public function store(StoreBookingRequest $request)
    {
        // Wajib terverifikasi sebelum memesan (gating sisi server)
        if (! Auth::user()->isVerified()) {
            return redirect()->route('customer.profile.edit')
                ->with('error', 'Selesaikan verifikasi akun (nomor telepon & foto SIM) dan tunggu konfirmasi admin sebelum melakukan pemesanan.');
        }

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

            // Cek ulang di dalam lock: hanya mobil 'maintenance' yang dilarang.
            // Mobil 'rented' tetap boleh dipesan untuk tanggal yang tidak bentrok
            // (divalidasi oleh pengecekan overlap di bawah).
            if ($car->status === 'maintenance') {
                throw ValidationException::withMessages([
                    'car_id' => 'Mobil sedang dalam perawatan dan tidak dapat disewa',
                ]);
            }

            // Cek booking yang tumpang-tindih, mempertimbangkan TANGGAL + JAM.
            // Dua rentang waktu [A_start, A_end) dan [B_start, B_end) overlap jika:
            //   A_start < B_end  AND  B_start < A_end
            // Disimpan sebagai DATETIME string (Y-m-d H:i:s) agar perbandingan akurat per jam.
            $overlapping = Booking::where('car_id', $request->car_id)
                ->blockingSlot()
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
                    ->blockingSlot()
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

        // Lazy-expire: jika batas waktu pembayaran sudah lewat dan belum dibayar,
        // batalkan otomatis di sini (berfungsi tanpa scheduler yang berjalan).
        if ($booking->isPaymentExpired()) {
            $booking->update(['status' => 'cancelled']);

            $minutes = (int) config('business.payment_window_minutes', 30);
            session()->flash('error', "Batas waktu pembayaran ({$minutes} menit) telah habis. Booking dibatalkan otomatis.");
        }

        return view('customer.bookings.show', compact('booking'));
    }

    /**
     * Upload payment proof.
     */
    public function uploadPayment(UploadPaymentRequest $request, $id)
    {
        $booking = Booking::where('user_id', Auth::id())->findOrFail($id);

        // Tolak jika booking sudah tidak menunggu pembayaran
        if ($booking->status !== 'pending' || $booking->payment_status !== 'unpaid') {
            return back()->with('error', 'Booking ini tidak dapat menerima pembayaran.');
        }

        // Tolak jika batas waktu pembayaran sudah lewat → batalkan otomatis
        if ($booking->isPaymentExpired()) {
            $booking->update(['status' => 'cancelled']);

            $minutes = (int) config('business.payment_window_minutes', 30);

            return back()->with('error', "Batas waktu pembayaran ({$minutes} menit) telah habis. Booking dibatalkan otomatis.");
        }

        if ($request->hasFile('payment_proof')) {
            // Delete old payment proof if exists (cek disk privat & legacy publik)
            if ($booking->payment_proof) {
                Storage::disk('local')->delete($booking->payment_proof);
                Storage::disk('public')->delete($booking->payment_proof);
            }

            // Simpan ke disk privat 'local' dengan nama acak (PII, bukan publik)
            $path = $request->file('payment_proof')->store('payments', 'local');

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
