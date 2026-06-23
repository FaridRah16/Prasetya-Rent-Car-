@extends('layouts.customer')

@section('title', 'Pembayaran')
@section('page-title', 'Pembayaran Booking #' . $booking->id)

@section('styles')
<style>
    .payment-wrapper {
        max-width: 800px;
        margin: 0 auto;
    }
    .payment-info-table td {
        padding: 0.4rem 0;
        border: none;
    }
    .payment-info-table td:first-child {
        color: #64748b;
        font-weight: 500;
        width: 200px;
    }
    .payment-info-table td:last-child {
        font-weight: 600;
        color: #1e293b;
    }
</style>
@endsection

@section('content')
<div class="payment-wrapper">
    <!-- Alert: Batas Waktu -->
    @php
        $paymentDeadline = $booking->paymentDeadline();
    @endphp
    @if($paymentDeadline)
        <div class="alert alert-danger d-flex align-items-center mb-4"
             id="paymentCountdown"
             data-deadline="{{ $paymentDeadline->timestamp * 1000 }}">
            <i class="bi bi-stopwatch-fill me-2 fs-3"></i>
            <div class="w-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        Selesaikan pembayaran dalam
                        <strong class="fs-5" id="countdownTimer">--:--</strong>
                    </div>
                    <div class="small text-end" style="min-width: 160px;">
                        Batas: {{ $paymentDeadline->copy()->timezone('Asia/Jakarta')->format('H:i') }} WIB
                    </div>
                </div>
                <div class="progress mt-2" style="height: 6px;">
                    <div class="progress-bar bg-danger" id="countdownBar" style="width: 100%;"></div>
                </div>
            </div>
        </div>
    @endif

    <!-- Nota Pembayaran -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-receipt"></i> Nota Pembayaran</h5>
        </div>
        <div class="card-body">
            <table class="table payment-info-table mb-0">
                <tr>
                    <td>Order ID</td>
                    <td class="font-monospace">{{ $booking->order_id }}</td>
                </tr>
                <tr>
                    <td>Mobil</td>
                    <td>{{ $booking->car->brand }} {{ $booking->car->model ?? $booking->car->name }}</td>
                </tr>
                <tr>
                    <td>Durasi Sewa</td>
                    <td>{{ $booking->total_days }} hari</td>
                </tr>
                <tr>
                    <td>Tanggal Sewa</td>
                    <td>
                        {{ $booking->start_date->format('d M Y') }}
                        @if($booking->pickup_time)
                            {{ \Carbon\Carbon::parse($booking->pickup_time)->format('H:i') }}
                        @endif
                        &mdash;
                        {{ $booking->end_date->format('d M Y') }}
                        @if($booking->return_time)
                            {{ \Carbon\Carbon::parse($booking->return_time)->format('H:i') }}
                        @endif
                    </td>
                </tr>
                @if($booking->driver)
                <tr>
                    <td>Driver</td>
                    <td>{{ $booking->driver->name }}</td>
                </tr>
                @endif
                <tr>
                    <td>Total Pembayaran</td>
                    <td><h4 class="text-primary mb-0">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</h4></td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Pilih Metode Pembayaran (Direct Redirect ke Midtrans Snap) -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-credit-card"></i> Pembayaran via Midtrans</h5>
        </div>
        <div class="card-body text-center py-4">
            <i class="bi bi-shield-check text-primary mb-3" style="font-size: 4rem;"></i>
            <h5>Pembayaran Aman</h5>
            <p class="text-muted mb-4">
                Transfer bank (BCA, BNI, BRI, Mandiri), GoPay, QRIS, dan lainnya.<br>
                Pembayaran diverifikasi otomatis.
            </p>

            <!-- Redirect langsung ke halaman pembayaran Midtrans Snap -->
            {{-- Format VT-Web: https://app.sandbox.midtrans.com/snap/v2/vtweb/{snap_token} --}}
            <a href="{{ $snapUrl }}"
               class="btn btn-primary btn-lg px-5"
               id="pay-button">
                <i class="bi bi-credit-card me-2"></i> Bayar Sekarang
            </a>
            <p class="text-muted small mt-3">
                Anda akan diarahkan ke halaman pembayaran Midtrans.
                <br>Setelah selesai, Anda akan kembali ke halaman booking.
            </p>
        </div>
    </div>

    <!-- Tombol Navigasi -->
    <div class="d-flex gap-2">
        <a href="{{ route('customer.bookings.show', $booking->id) }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left"></i> Kembali ke Detail Booking
        </a>
        <a href="{{ route('customer.bookings.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-list"></i> Daftar Booking
        </a>
    </div>
</div>
@endsection

@section('scripts')
<script>
    (function () {
        // ========== Countdown Timer ==========
        const box = document.getElementById('paymentCountdown');
        if (box) {
            const timerEl = document.getElementById('countdownTimer');
            const barEl = document.getElementById('countdownBar');
            const deadline = parseInt(box.dataset.deadline, 10);
            const totalDuration = deadline - Date.now();

            function pad(n) { return n < 10 ? '0' + n : '' + n; }

            function tick() {
                const remaining = deadline - Date.now();

                if (remaining <= 0) {
                    timerEl.textContent = '00:00';
                    if (barEl) barEl.style.width = '0%';
                    clearInterval(interval);
                    window.location.reload();
                    return;
                }

                const totalSeconds = Math.floor(remaining / 1000);
                const minutes = Math.floor(totalSeconds / 60);
                const seconds = totalSeconds % 60;
                timerEl.textContent = pad(minutes) + ':' + pad(seconds);

                if (barEl && totalDuration > 0) {
                    barEl.style.width = Math.max(0, (remaining / totalDuration) * 100) + '%';
                }
            }

            tick();
            const interval = setInterval(tick, 1000);
        }
    })();
</script>
@endsection
