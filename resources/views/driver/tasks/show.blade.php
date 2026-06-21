@extends('layouts.driver')

@section('title', 'Detail Tugas')
@section('page-title', 'Detail Tugas #' . $task->id)

@section('styles')
<style>
    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
    }
    
    .timeline {
        position: relative;
        padding-left: 30px;
    }
    
    .timeline::before {
        content: '';
        position: absolute;
        left: 8px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #dee2e6;
    }
    
    .timeline-item {
        position: relative;
        padding-bottom: 1.5rem;
    }
    
    .timeline-item::before {
        content: '';
        position: absolute;
        left: -26px;
        top: 5px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #fff;
        border: 2px solid #1a3c5e;
    }
    
    .timeline-item.active::before {
        background: #1a3c5e;
    }
    
    .map-placeholder {
        height: 200px;
        background: linear-gradient(135deg, #667eea, #764ba2);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <!-- Status Card -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1">Status Tugas</h5>
                        <p class="text-muted mb-0">ID: #{{ $task->id }}</p>
                    </div>
                    <div class="text-end">
                        @if($task->status === 'confirmed')
                            <span class="status-badge bg-info text-white">
                                <i class="bi bi-check-circle"></i> Menunggu Dimulai
                            </span>
                        @elseif($task->status === 'ongoing' && $task->delivery_proof)
                            <span class="status-badge bg-warning text-dark">
                                <i class="bi bi-hourglass-split"></i> Menunggu Konfirmasi Admin
                            </span>
                        @elseif($task->status === 'ongoing')
                            <span class="status-badge bg-primary text-white">
                                <i class="bi bi-car-front"></i> Sedang Berlangsung
                            </span>
                        @elseif($task->status === 'completed')
                            <span class="status-badge bg-success text-white">
                                <i class="bi bi-check-all"></i> Selesai
                            </span>
                        @else
                            <span class="status-badge bg-warning text-dark">
                                <i class="bi bi-clock"></i> {{ ucfirst($task->status) }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Information -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-person-circle"></i> Informasi Customer</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-2">
                            <strong><i class="bi bi-person"></i> Nama:</strong><br>
                            {{ $task->user->name }}
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-2">
                            <strong><i class="bi bi-telephone"></i> Telepon:</strong><br>
                            <a href="tel:{{ $task->user->phone }}" class="text-decoration-none">
                                {{ $task->user->phone }}
                            </a>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-0">
                            <strong><i class="bi bi-envelope"></i> Email:</strong><br>
                            <a href="mailto:{{ $task->user->email }}" class="text-decoration-none">
                                {{ $task->user->email }}
                            </a>
                        </p>
                    </div>
                    @if($task->user->whatsapp_number)
                        <div class="col-md-6">
                            <p class="mb-0">
                                <strong><i class="bi bi-whatsapp text-success"></i> WhatsApp:</strong><br>
                                <a href="https://wa.me/{{ formatWhatsAppNumber($task->user->whatsapp_number) }}" 
                                   target="_blank" 
                                   class="text-decoration-none text-success">
                                    {{ $task->user->whatsapp_number }}
                                </a>
                            </p>
                        </div>
                    @endif
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
                        @if($task->car->image)
                            <img src="{{ asset('storage/' . $task->car->image) }}" 
                                 alt="{{ $task->car->name }}" 
                                 class="img-fluid rounded"
                                 style="max-height: 120px; object-fit: cover;">
                        @else
                            <div style="height: 120px; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 10px;" class="d-flex align-items-center justify-content-center">
                                <i class="bi bi-car-front-fill text-white" style="font-size: 3rem;"></i>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-9">
                        <h4>{{ $task->car->name }}</h4>
                        <p class="text-muted mb-2">
                            {{ $task->car->brand }} • {{ $task->car->type }} • {{ $task->car->year }}
                        </p>
                        <div class="row">
                            <div class="col-6">
                                <small class="text-muted">Plat Nomor</small>
                                <p class="mb-0 fw-bold">{{ $task->car->plate_number }}</p>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Warna</small>
                                <p class="mb-0 fw-bold">{{ $task->car->color }}</p>
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
                        <p class="mb-2">
                            <i class="bi bi-calendar-event text-primary"></i>
                            <strong>Tanggal & Jam Mulai</strong><br>
                            <span class="ms-4">
                                {{ $task->start_date->format('d F Y') }}
                                @if($task->pickup_time)
                                    <span class="badge bg-info">{{ \Carbon\Carbon::parse($task->pickup_time)->format('H:i') }} WIB</span>
                                @endif
                            </span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-2">
                            <i class="bi bi-calendar-check text-success"></i>
                            <strong>Tanggal & Jam Selesai</strong><br>
                            <span class="ms-4">
                                {{ $task->end_date->format('d F Y') }}
                                @if($task->return_time)
                                    <span class="badge bg-info">{{ \Carbon\Carbon::parse($task->return_time)->format('H:i') }} WIB</span>
                                @endif
                            </span>
                        </p>
                    </div>
                </div>
                
                <div class="alert alert-info">
                    <strong><i class="bi bi-clock"></i> Durasi:</strong> {{ $task->total_days }} hari
                </div>
            </div>
        </div>

        <!-- Location Details -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-geo-alt"></i> Detail Lokasi</h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item active">
                        <h6 class="mb-1"><i class="bi bi-geo-alt-fill text-success"></i> Lokasi Penjemputan</h6>
                        <p class="mb-1 text-muted">{{ $task->pickup_location }}</p>
                        @if($task->pickup_lat && $task->pickup_lng)
                            <a href="https://www.google.com/maps?q={{ $task->pickup_lat }},{{ $task->pickup_lng }}" target="_blank" class="small">
                                <i class="bi bi-map"></i> Lihat di Google Maps
                            </a>
                        @endif
                    </div>
                    <div class="timeline-item">
                        <h6 class="mb-1"><i class="bi bi-geo-fill text-danger"></i> Lokasi Pengantaran</h6>
                        <p class="mb-1 text-muted">{{ $task->dropoff_location }}</p>
                        @if($task->dropoff_lat && $task->dropoff_lng)
                            <a href="https://www.google.com/maps?q={{ $task->dropoff_lat }},{{ $task->dropoff_lng }}" target="_blank" class="small">
                                <i class="bi bi-map"></i> Lihat di Google Maps
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Notes -->
        @if($task->notes)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-journal-text"></i> Catatan Customer</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $task->notes }}</p>
                </div>
            </div>
        @endif
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Quick Actions -->
        <div class="card mb-4">
            <div class="card-header bg-warning">
                <h5 class="mb-0"><i class="bi bi-lightning"></i> Aksi Cepat</h5>
            </div>
            <div class="card-body">
                @if($task->status === 'confirmed')
                    <form method="POST" action="{{ route('driver.tasks.start', $task->id) }}">
                        @csrf
                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            <i class="bi bi-play-circle"></i> Mulai Tugas
                        </button>
                    </form>
                    <p class="small text-muted mb-0">
                        <i class="bi bi-info-circle"></i> Klik tombol di atas saat Anda siap memulai tugas
                    </p>
                @elseif($task->status === 'ongoing')
                    @if($task->delivery_proof)
                        <div class="alert alert-success mb-3">
                            <i class="bi bi-check-circle"></i> <strong>Bukti pengantaran sudah dikirim</strong>
                        </div>
                        <div class="mb-3">
                            <img src="{{ route('secure.delivery', $task->id) }}"
                                 alt="Bukti Pengantaran"
                                 class="img-fluid rounded" 
                                 style="max-height: 200px; width: 100%; object-fit: cover;">
                        </div>
                        <p class="small text-muted mb-0">
                            <i class="bi bi-info-circle"></i> Menunggu admin menyelesaikan pemesanan
                        </p>
                    @else
                        <form method="POST" action="{{ route('driver.tasks.complete', $task->id) }}" 
                              enctype="multipart/form-data" 
                              onsubmit="return confirm('Pastikan foto bukti pengantaran sudah benar.')">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Foto Bukti Pengantaran <span class="text-danger">*</span></label>
                                <input type="file" 
                                       class="form-control @error('delivery_proof') is-invalid @enderror" 
                                       name="delivery_proof" 
                                       accept="image/*" 
                                       capture="environment"
                                       required>
                                @error('delivery_proof')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Foto mobil yang sudah diserahkan ke customer</small>
                            </div>
                            <button type="submit" class="btn btn-success w-100 mb-2">
                                <i class="bi bi-camera"></i> Kirim Bukti Pengantaran
                            </button>
                        </form>
                        <p class="small text-muted mb-0">
                            <i class="bi bi-info-circle"></i> Upload foto bukti mobil sudah diterima customer
                        </p>
                    @endif
                @else
                    <div class="alert alert-success mb-0">
                        <i class="bi bi-check-circle"></i> Tugas telah diselesaikan oleh admin
                    </div>
                @endif
            </div>
        </div>

        <!-- Contact Customer -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-telephone"></i> Hubungi Customer</h5>
            </div>
            <div class="card-body d-grid gap-2">
                <a href="tel:{{ $task->user->phone }}" class="btn btn-outline-primary">
                    <i class="bi bi-telephone-fill"></i> Telepon
                </a>
                <a href="https://wa.me/{{ formatWhatsAppNumber($task->user->whatsapp_number ?: $task->user->phone) }}" 
                   target="_blank" 
                   class="btn btn-outline-success">
                    <i class="bi bi-whatsapp"></i> WhatsApp
                </a>
            </div>
        </div>

        <!-- Payment Info -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-credit-card"></i> Informasi Pembayaran</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Total Biaya</span>
                    <strong>Rp {{ number_format($task->total_price, 0, ',', '.') }}</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Status</span>
                    @if($task->payment_status === 'paid')
                        <span class="badge bg-success">Lunas</span>
                    @else
                        <span class="badge bg-danger">Belum Bayar</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Back Button -->
        <a href="{{ route('driver.tasks.index') }}" class="btn btn-secondary w-100">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar Tugas
        </a>
    </div>
</div>
@endsection
