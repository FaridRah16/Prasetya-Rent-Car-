@extends('layouts.admin')

@section('title', 'Detail Booking #' . $booking->id)
@section('page-title', 'Detail Booking #' . $booking->id)

@section('styles')
<style>
    .payment-proof-img {
        max-width: 400px;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0,0,0,.1);
    }
    
    .info-box {
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 8px;
        border-left: 4px solid var(--primary-color);
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <!-- Status Management -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-sliders"></i> Kelola Status</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.bookings.updateStatus', $booking->id) }}">
                    @csrf
                    <div class="row align-items-end g-3">
                        <div class="col-md-8">
                            <label class="form-label">Status Booking</label>
                            <select class="form-select" name="status" required>
                                <option value="pending" {{ $booking->status == 'pending' ? 'selected' : '' }}>
                                    Pending - Menunggu konfirmasi
                                </option>
                                <option value="confirmed" {{ $booking->status == 'confirmed' ? 'selected' : '' }}>
                                    Dikonfirmasi - Booking telah dikonfirmasi
                                </option>
                                <option value="ongoing" {{ $booking->status == 'ongoing' ? 'selected' : '' }}>
                                    Berlangsung - Mobil sedang digunakan
                                </option>
                                <option value="completed" {{ $booking->status == 'completed' ? 'selected' : '' }}>
                                    Selesai - Sewa telah selesai
                                </option>
                                <option value="cancelled" {{ $booking->status == 'cancelled' ? 'selected' : '' }}>
                                    Dibatalkan - Booking dibatalkan
                                </option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-check-circle"></i> Update Status
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Customer Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-person"></i> Informasi Customer</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <small class="text-muted">Nama</small>
                        <p class="mb-0 fw-bold">{{ $booking->user->name }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted">Email</small>
                        <p class="mb-0">
                            <a href="mailto:{{ $booking->user->email }}" class="text-decoration-none">{{ $booking->user->email }}</a>
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted">No. Telepon</small>
                        <p class="mb-0">
                            <a href="tel:{{ $booking->user->phone }}" class="text-decoration-none">{{ $booking->user->phone }}</a>
                        </p>
                    </div>
                    @if($booking->user->whatsapp_number)
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">No. WhatsApp</small>
                            <p class="mb-0">
                                <a href="https://wa.me/{{ formatWhatsAppNumber($booking->user->whatsapp_number) }}" 
                                   target="_blank" rel="noopener noreferrer" 
                                   class="text-decoration-none text-success">
                                    <i class="bi bi-whatsapp"></i> {{ $booking->user->whatsapp_number }}
                                </a>
                            </p>
                        </div>
                    @endif
                </div>

                <!-- Hubungi Customer Buttons -->
                <div class="d-flex gap-2 mt-3 pt-3 border-top">
                    <a href="tel:{{ $booking->user->phone }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-telephone-fill"></i> Telepon
                    </a>
                    @php
                        $waNumber = formatWhatsAppNumber($booking->user->whatsapp_number ?: $booking->user->phone);
                    @endphp
                    <a href="https://wa.me/{{ $waNumber }}" 
                       target="_blank" rel="noopener noreferrer" 
                       class="btn btn-outline-success btn-sm">
                        <i class="bi bi-whatsapp"></i> WhatsApp
                    </a>
                </div>
            </div>
        </div>

        <!-- Car Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-car-front"></i> Informasi Mobil</h5>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <div style="height: 100px; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 10px;" class="d-flex align-items-center justify-content-center">
                            <i class="bi bi-car-front-fill text-white" style="font-size: 3rem;"></i>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <h5 class="mb-2">{{ $booking->car->name }}</h5>
                        <div class="row">
                            <div class="col-6">
                                <small class="text-muted">Brand/Tipe</small>
                                <p class="mb-2">{{ $booking->car->brand }} • {{ $booking->car->type }}</p>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Plat Nomor</small>
                                <p class="mb-2">{{ $booking->car->plate_number }}</p>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Tahun</small>
                                <p class="mb-0">{{ $booking->car->year }}</p>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Kapasitas</small>
                                <p class="mb-0">{{ $booking->car->seats }} Kursi</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Booking Details -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-calendar-check"></i> Detail Booking</h5>
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
                        <p class="mb-0"><i class="bi bi-geo-alt-fill text-primary"></i> {{ $booking->pickup_location }}</p>
                        @if($booking->pickup_lat && $booking->pickup_lng)
                            <a href="https://www.google.com/maps?q={{ $booking->pickup_lat }},{{ $booking->pickup_lng }}" target="_blank" rel="noopener noreferrer" class="small">
                                <i class="bi bi-map"></i> Lihat di Google Maps
                            </a>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">Lokasi Pengantaran</small>
                        <p class="mb-0"><i class="bi bi-geo-alt-fill text-success"></i> {{ $booking->dropoff_location }}</p>
                        @if($booking->dropoff_lat && $booking->dropoff_lng)
                            <a href="https://www.google.com/maps?q={{ $booking->dropoff_lat }},{{ $booking->dropoff_lng }}" target="_blank" rel="noopener noreferrer" class="small">
                                <i class="bi bi-map"></i> Lihat di Google Maps
                            </a>
                        @endif
                    </div>
                </div>
                @if($booking->notes)
                    <div class="info-box">
                        <small class="text-muted d-block mb-1">Catatan Customer:</small>
                        <p class="mb-0">{{ $booking->notes }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Driver Management -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-person-badge"></i> Kelola Driver</h5>
            </div>
            <div class="card-body">
                @if($booking->driver_id)
                    <div class="alert alert-info mb-3">
                        <strong>Driver Saat Ini:</strong> {{ $booking->driver->name ?? '-' }}
                    </div>
                @else
                    <div class="alert alert-warning mb-3">
                        <i class="bi bi-exclamation-triangle"></i> Belum ada driver yang ditugaskan
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.bookings.assignDriver', $booking->id) }}">
                    @csrf
                    <div class="row align-items-end g-3">
                        <div class="col-md-8">
                            <label class="form-label">Pilih Driver</label>
                            <select class="form-select" name="driver_id" required>
                                <option value="">-- Pilih Driver --</option>
                                @foreach($availableDrivers as $driver)
                                    <option value="{{ $driver->user_id }}" 
                                            {{ $booking->driver_id == $driver->user_id ? 'selected' : '' }}>
                                        {{ $driver->user->name }} - SIM: {{ $driver->license_number }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-person-plus"></i> Tugaskan Driver
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Payment Verification -->
        @if($booking->payment_proof)
            <div class="card mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="bi bi-image"></i> Bukti Pembayaran</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <img src="{{ route('secure.payment', $booking->id) }}"
                             alt="Payment Proof"
                             class="payment-proof-img img-fluid">
                    </div>

                    @if($booking->payment_status === 'unpaid')
                        <div class="d-flex gap-2">
                            <form method="POST" action="{{ route('admin.bookings.verifyPayment', $booking->id) }}" class="flex-fill">
                                @csrf
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="bi bi-check-circle"></i> Verifikasi Pembayaran
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.bookings.rejectPayment', $booking->id) }}" 
                                  onsubmit="return confirm('Yakin ingin menolak bukti pembayaran ini?')" 
                                  class="flex-fill">
                                @csrf
                                <button type="submit" class="btn btn-danger w-100">
                                    <i class="bi bi-x-circle"></i> Tolak Pembayaran
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="alert alert-success mb-0">
                            <i class="bi bi-check-circle"></i> Pembayaran telah diverifikasi
                        </div>
                    @endif
                </div>
            </div>
        @else
            <div class="card mb-4">
                <div class="card-body text-center py-4">
                    <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mb-0 mt-2">Belum ada bukti pembayaran yang diupload</p>
                </div>
            </div>
        @endif

        <!-- Delivery Proof (Bukti Pengantaran) -->
        @if($booking->delivery_proof)
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-camera"></i> Bukti Pengantaran</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-3">
                        <i class="bi bi-info-circle"></i> Driver telah mengirim bukti pengantaran. Silakan periksa dan selesaikan pemesanan jika sudah sesuai.
                    </div>
                    <div class="mb-3">
                        <img src="{{ route('secure.delivery', $booking->id) }}"
                             alt="Bukti Pengantaran"
                             class="payment-proof-img img-fluid">
                    </div>

                    @if($booking->status === 'ongoing')
                        <form method="POST" action="{{ route('admin.bookings.updateStatus', $booking->id) }}" 
                              onsubmit="return confirm('Yakin ingin menyelesaikan pemesanan ini?')">
                            @csrf
                            <input type="hidden" name="status" value="completed">
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-check-circle"></i> Selesaikan Pemesanan
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Summary -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-receipt"></i> Ringkasan</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted">Status Booking</small>
                    <p class="mb-0">
                        @if($booking->status === 'pending')
                            <span class="badge bg-warning text-dark">Pending</span>
                        @elseif($booking->status === 'confirmed')
                            <span class="badge bg-info">Dikonfirmasi</span>
                        @elseif($booking->status === 'ongoing' && $booking->delivery_proof)
                            <span class="badge bg-warning text-dark">Menunggu Selesai</span>
                        @elseif($booking->status === 'ongoing')
                            <span class="badge bg-primary">Berlangsung</span>
                        @elseif($booking->status === 'completed')
                            <span class="badge bg-success">Selesai</span>
                        @else
                            <span class="badge bg-danger">Dibatalkan</span>
                        @endif
                    </p>
                </div>

                <div class="mb-3">
                    <small class="text-muted">Status Pembayaran</small>
                    <p class="mb-0">
                        @if($booking->payment_status === 'paid')
                            <span class="badge bg-success">Lunas</span>
                        @else
                            <span class="badge bg-warning text-dark">Belum Bayar</span>
                        @endif
                    </p>
                </div>

                <hr>

                <div class="d-flex justify-content-between mb-2">
                    <span>Harga per Hari</span>
                    <strong>Rp {{ number_format($booking->car->price_per_day, 0, ',', '.') }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span>Durasi</span>
                    <strong>{{ $booking->total_days }} hari</strong>
                </div>

                <hr>

                <div class="d-flex justify-content-between">
                    <strong>Total</strong>
                    <h4 class="text-primary mb-0">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-primary w-100">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar
        </a>
    </div>
</div>
@endsection
