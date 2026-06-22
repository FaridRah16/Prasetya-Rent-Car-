@extends('layouts.customer')

@section('title', 'Detail Booking')
@section('page-title', 'Detail Booking #' . $booking->id)

@section('styles')
<style>
    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
    }
    
    .payment-proof-preview {
        max-width: 300px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,.1);
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <!-- Booking Status -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1">Status Booking</h5>
                        <p class="text-muted mb-0">ID: #{{ $booking->id }}</p>
                    </div>
                    <div class="text-end">
                        @if($booking->status === 'pending')
                            <span class="status-badge bg-warning text-dark">
                                <i class="bi bi-clock"></i> Menunggu Konfirmasi
                            </span>
                        @elseif($booking->status === 'confirmed')
                            <span class="status-badge bg-info text-white">
                                <i class="bi bi-check-circle"></i> Dikonfirmasi
                            </span>
                        @elseif($booking->status === 'ongoing')
                            <span class="status-badge bg-primary text-white">
                                <i class="bi bi-car-front"></i> Sedang Berlangsung
                            </span>
                        @elseif($booking->status === 'completed')
                            <span class="status-badge bg-success text-white">
                                <i class="bi bi-check-all"></i> Selesai
                            </span>
                        @else
                            <span class="status-badge bg-danger text-white">
                                <i class="bi bi-x-circle"></i> Dibatalkan
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Car Details -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-car-front"></i> Detail Mobil</h5>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <div style="height: 120px; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 10px;" class="d-flex align-items-center justify-content-center">
                            <i class="bi bi-car-front-fill text-white" style="font-size: 4rem;"></i>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <h4>{{ $booking->car->name }}</h4>
                        <p class="text-muted mb-2">
                            {{ $booking->car->brand }} • {{ $booking->car->type }} • {{ $booking->car->year }}
                        </p>
                        <div class="row">
                            <div class="col-6">
                                <small class="text-muted">Kapasitas</small>
                                <p class="mb-0 fw-bold">{{ $booking->car->seats }} Kursi</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Booking Details -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-calendar-check"></i> Detail Sewa</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <small class="text-muted">Tanggal & Jam Mulai</small>
                        <p class="mb-0 fw-bold">
                            {{ $booking->start_date->format('d M Y') }}
                            @if($booking->pickup_time)
                                <span class="badge bg-info">{{ \Carbon\Carbon::parse($booking->pickup_time)->format('H:i') }} WIB</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">Tanggal & Jam Selesai</small>
                        <p class="mb-0 fw-bold">
                            {{ $booking->end_date->format('d M Y') }}
                            @if($booking->return_time)
                                <span class="badge bg-info">{{ \Carbon\Carbon::parse($booking->return_time)->format('H:i') }} WIB</span>
                            @endif
                        </p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <small class="text-muted">Lokasi Penjemputan</small>
                        <p class="mb-0">
                            <i class="bi bi-geo-alt"></i> {{ $booking->pickup_location }}
                            @if($booking->pickup_lat && $booking->pickup_lng)
                                <br><a href="https://www.google.com/maps?q={{ $booking->pickup_lat }},{{ $booking->pickup_lng }}" target="_blank" rel="noopener noreferrer" class="small text-primary">
                                    <i class="bi bi-map"></i> Lihat di Google Maps
                                </a>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">Lokasi Pengantaran</small>
                        <p class="mb-0">
                            <i class="bi bi-geo-alt"></i> {{ $booking->dropoff_location }}
                            @if($booking->dropoff_lat && $booking->dropoff_lng)
                                <br><a href="https://www.google.com/maps?q={{ $booking->dropoff_lat }},{{ $booking->dropoff_lng }}" target="_blank" rel="noopener noreferrer" class="small text-primary">
                                    <i class="bi bi-map"></i> Lihat di Google Maps
                                </a>
                            @endif
                        </p>
                    </div>
                </div>
                @if($booking->driver_id)
                    <div class="row mb-3">
                        <div class="col-12">
                            <small class="text-muted">Driver</small>
                            <p class="mb-0">
                                <i class="bi bi-person"></i> {{ $booking->driver->name ?? '-' }}
                                @if($booking->driver)
                                    @if($booking->driver->phone)
                                        <small class="text-muted">({{ $booking->driver->phone }})</small>
                                    @endif
                                    @php $driverContact = $booking->driver->whatsapp_number ?: $booking->driver->phone; @endphp
                                    @if($driverContact)
                                        <a href="https://wa.me/{{ formatWhatsAppNumber($driverContact) }}"
                                           target="_blank" rel="noopener noreferrer"
                                           class="small text-success ms-1">
                                            <i class="bi bi-whatsapp"></i> WhatsApp
                                        </a>
                                    @endif
                                @endif
                            </p>
                        </div>
                    </div>
                @endif
                @if($booking->notes)
                    <div class="row">
                        <div class="col-12">
                            <small class="text-muted">Catatan</small>
                            <p class="mb-0">{{ $booking->notes }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Payment Proof Upload -->
        @if($booking->status === 'pending' && $booking->payment_status === 'unpaid')
            <div class="card mb-4">
                <div class="card-header bg-warning">
                    <h5 class="mb-0"><i class="bi bi-upload"></i> Upload Bukti Pembayaran</h5>
                </div>
                <div class="card-body">
                    @php
                        $paymentDeadline = $booking->paymentDeadline();
                    @endphp
                    @if($paymentDeadline)
                        <div class="alert alert-danger d-flex align-items-center mb-3"
                             id="paymentCountdown"
                             data-deadline="{{ $paymentDeadline->timestamp * 1000 }}">
                            <i class="bi bi-stopwatch-fill me-2 fs-3"></i>
                            <div>
                                Selesaikan pembayaran dalam
                                <strong class="fs-5" id="countdownTimer">--:--</strong>
                                <div class="small">
                                    Batas waktu: {{ $paymentDeadline->copy()->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB.
                                    Lewat dari ini booking dibatalkan otomatis.
                                </div>
                            </div>
                        </div>
                    @endif

                    <p class="mb-3">Silakan lakukan pembayaran sejumlah <strong>Rp {{ number_format($booking->total_price, 0, ',', '.') }}</strong> ke rekening:</p>

                    <div class="alert alert-info">
                        <strong>{{ config('payment.bank_name') }}</strong><br>
                        No. Rekening: <strong>{{ config('payment.account_number') }}</strong><br>
                        A/N: <strong>{{ config('payment.account_name') }}</strong>
                    </div>

                    <form method="POST" action="{{ route('customer.bookings.uploadPayment', $booking->id) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Upload Bukti Transfer</label>
                            <input type="file" 
                                   class="form-control @error('payment_proof') is-invalid @enderror" 
                                   name="payment_proof" 
                                   accept="image/*" 
                                   required>
                            @error('payment_proof')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Format: JPG, PNG (Max: 2MB)</small>
                        </div>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-upload"></i> Upload Bukti Bayar
                        </button>
                    </form>
                </div>
            </div>
        @endif

        <!-- Show Payment Proof if Uploaded -->
        @if($booking->payment_proof)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-image"></i> Bukti Pembayaran</h5>
                </div>
                <div class="card-body">
                    <img src="{{ route('secure.payment', $booking->id) }}"
                         alt="Payment Proof"
                         class="payment-proof-preview img-fluid">
                    <p class="mt-3 mb-0">
                        @if($booking->payment_status === 'paid')
                            <span class="badge bg-success">
                                <i class="bi bi-check-circle"></i> Pembayaran Terverifikasi
                            </span>
                        @else
                            <span class="badge bg-warning text-dark">
                                <i class="bi bi-clock"></i> Menunggu Verifikasi Admin
                            </span>
                        @endif
                    </p>
                </div>
            </div>
        @endif
    </div>

    <!-- Summary Sidebar -->
    <div class="col-lg-4">
        <!-- Price Summary -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-receipt"></i> Ringkasan Pembayaran</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Harga per Hari</span>
                    <span class="fw-bold">Rp {{ number_format($booking->car->price_per_day, 0, ',', '.') }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Durasi</span>
                    <span class="fw-bold">{{ $booking->total_days }} hari</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-0">
                    <strong>Total</strong>
                    <h4 class="text-primary mb-0">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>

        <!-- Actions -->
        @if($booking->status === 'pending')
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Aksi</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('customer.bookings.cancel', $booking->id) }}" onsubmit="return confirm('Yakin ingin membatalkan booking ini?')">
                        @csrf
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="bi bi-x-circle"></i> Batalkan Booking
                        </button>
                    </form>
                </div>
            </div>
        @endif

        <a href="{{ route('customer.bookings.index') }}" class="btn btn-outline-primary w-100 mt-3">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar Booking
        </a>
    </div>
</div>
@endsection

@section('scripts')
<script>
    (function () {
        const box = document.getElementById('paymentCountdown');
        if (!box) return;

        const timerEl = document.getElementById('countdownTimer');
        const deadline = parseInt(box.dataset.deadline, 10); // epoch ms
        const form = box.closest('.card')?.querySelector('form[action*="upload-payment"]');

        function pad(n) { return n < 10 ? '0' + n : '' + n; }

        function tick() {
            const remaining = deadline - Date.now();

            if (remaining <= 0) {
                timerEl.textContent = '00:00';
                box.classList.remove('alert-danger');
                box.classList.add('alert-secondary');
                timerEl.textContent = 'Waktu habis';
                if (form) {
                    form.querySelectorAll('input, button').forEach(el => el.disabled = true);
                }
                clearInterval(interval);
                // Muat ulang agar status booking (dibatalkan) ter-update dari server
                setTimeout(() => window.location.reload(), 1500);
                return;
            }

            const totalSeconds = Math.floor(remaining / 1000);
            const minutes = Math.floor(totalSeconds / 60);
            const seconds = totalSeconds % 60;
            timerEl.textContent = pad(minutes) + ':' + pad(seconds);

            // Beri peringatan visual saat < 5 menit
            if (remaining <= 5 * 60 * 1000) {
                box.classList.add('border', 'border-danger');
            }
        }

        tick();
        const interval = setInterval(tick, 1000);
    })();
</script>
@endsection
