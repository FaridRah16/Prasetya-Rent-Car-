@extends('layouts.driver')

@section('title', 'Dashboard Driver')
@section('page-title', 'Dashboard Driver')

@section('content')

<div class="row">
    <div class="col-md-4">
        <div class="stat-card position-relative" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <h3>{{ $activeTasks }}</h3>
            <p>Tugas Aktif</p>
            <i class="bi bi-clipboard-check"></i>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card position-relative" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <h3>{{ $ongoingTasks }}</h3>
            <p>Sedang Berlangsung</p>
            <i class="bi bi-car-front"></i>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card position-relative" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
            <h3>{{ $completedTasks }}</h3>
            <p>Tugas Selesai</p>
            <i class="bi bi-check-circle"></i>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Selamat Datang, {{ Auth::user()->name }}!</h5>
                @if($driverStatus === 'available')
                    <span class="badge bg-success">Tersedia</span>
                @else
                    <span class="badge bg-primary">Sedang Bertugas</span>
                @endif
            </div>
            <div class="card-body">
                @if($activeTasks > 0)
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> 
                        Anda memiliki <strong>{{ $activeTasks }}</strong> tugas aktif. 
                        Silakan cek di <a href="{{ route('driver.tasks.index') }}">Tugas Saya</a>.
                    </div>
                @else
                    <p class="mb-3">Saat ini Anda tidak memiliki tugas aktif.</p>
                    <p class="text-muted">Sistem akan memberitahu Anda ketika ada booking baru yang ditugaskan.</p>
                @endif

                <hr>

                <h6>Quick Links:</h6>
                <div class="d-flex gap-2">
                    <a href="{{ route('driver.tasks.index') }}" class="btn btn-primary">
                        <i class="bi bi-list-check"></i> Tugas Aktif
                    </a>
                    <a href="{{ route('driver.tasks.history') }}" class="btn btn-outline-primary">
                        <i class="bi bi-clock-history"></i> Riwayat
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Tasks -->
        @if($recentTasks->count() > 0)
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Tugas Terbaru</h5>
                </div>
                <div class="card-body">
                    @foreach($recentTasks as $task)
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                            <div>
                                <h6 class="mb-1">Booking #{{ $task->id }}</h6>
                                <small class="text-muted">
                                    {{ $task->car->name }} - {{ $task->user->name }}
                                </small>
                            </div>
                            <div class="text-end">
                                @if($task->status === 'confirmed')
                                    <span class="badge bg-info">Dikonfirmasi</span>
                                @elseif($task->status === 'ongoing')
                                    <span class="badge bg-primary">Berlangsung</span>
                                @elseif($task->status === 'completed')
                                    <span class="badge bg-success">Selesai</span>
                                @else
                                    <span class="badge bg-danger">Dibatalkan</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Informasi Driver</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted">Nama</small>
                    <p class="mb-0 fw-bold">{{ Auth::user()->name }}</p>
                </div>
                <div class="mb-3">
                    <small class="text-muted">No. SIM</small>
                    <p class="mb-0 fw-bold">{{ Auth::user()->driver ? Auth::user()->driver->license_number : '-' }}</p>
                </div>
                <div class="mb-3">
                    <small class="text-muted">Telepon</small>
                    <p class="mb-0">{{ Auth::user()->phone }}</p>
                </div>
                <div>
                    <small class="text-muted">Status</small>
                    <p class="mb-0">
                        @if($driverStatus === 'available')
                            <span class="badge bg-success">Tersedia</span>
                        @else
                            <span class="badge bg-primary">Sedang Bertugas</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">Tips</h5>
            </div>
            <div class="card-body">
                <ul class="mb-0 small">
                    <li>Pastikan mobil dalam kondisi baik sebelum berangkat</li>
                    <li>Hubungi customer sebelum penjemputan</li>
                    <li>Konfirmasi lokasi penjemputan yang akurat</li>
                    <li>Selesaikan tugas setelah pengantaran</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
