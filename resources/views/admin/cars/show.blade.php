@extends('layouts.admin')

@section('title', 'Detail Mobil')
@section('page-title', 'Detail Mobil: ' . $car->name)

@section('content')
<div class="row">
    <div class="col-lg-8">
        <!-- Car Image -->
        <div class="card mb-4">
            <div class="card-body">
                @if($car->image)
                    <img src="{{ asset('storage/' . $car->image) }}" 
                         alt="{{ $car->name }}" 
                         class="img-fluid rounded" 
                         style="width: 100%; max-height: 400px; object-fit: cover;">
                @else
                    <div style="height: 300px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);" 
                         class="d-flex align-items-center justify-content-center rounded">
                        <i class="bi bi-car-front-fill text-white" style="font-size: 8rem;"></i>
                    </div>
                @endif
            </div>
        </div>

        <!-- Car Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-info-circle"></i> Informasi Mobil</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <small class="text-muted">Nama Mobil</small>
                        <p class="mb-0 fw-bold fs-5">{{ $car->name }}</p>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">Status</small>
                        <p class="mb-0">
                            @if($car->status === 'available')
                                <span class="badge bg-success">Tersedia</span>
                            @elseif($car->status === 'rented')
                                <span class="badge bg-primary">Disewa</span>
                            @else
                                <span class="badge bg-warning text-dark">Maintenance</span>
                            @endif
                        </p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <small class="text-muted">Brand</small>
                        <p class="mb-0 fw-bold">{{ $car->brand }}</p>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted">Tipe</small>
                        <p class="mb-0 fw-bold">{{ $car->type }}</p>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted">Tahun</small>
                        <p class="mb-0 fw-bold">{{ $car->year }}</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <small class="text-muted">Warna</small>
                        <p class="mb-0 fw-bold">{{ $car->color }}</p>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted">Plat Nomor</small>
                        <p class="mb-0 fw-bold">{{ $car->plate_number }}</p>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted">Kapasitas</small>
                        <p class="mb-0 fw-bold">{{ $car->seats }} Kursi</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <small class="text-muted">Harga Sewa</small>
                        <h4 class="text-primary mb-0">Rp {{ number_format($car->price_per_day, 0, ',', '.') }} <small class="text-muted fw-normal">/ hari</small></h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Description -->
        @if($car->description)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-chat-left-text"></i> Deskripsi</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $car->description }}</p>
                </div>
            </div>
        @endif

        <!-- Booking History -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-clock-history"></i> Riwayat Booking</h5>
            </div>
            <div class="card-body">
                @php
                    $bookings = $car->bookings()->with('user')->orderBy('created_at', 'desc')->take(5)->get();
                @endphp

                @if($bookings->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Customer</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bookings as $booking)
                                    <tr>
                                        <td>#{{ $booking->id }}</td>
                                        <td>{{ $booking->user->name }}</td>
                                        <td>{{ $booking->start_date->format('d M Y') }}</td>
                                        <td>
                                            @if($booking->status === 'pending')
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            @elseif($booking->status === 'confirmed')
                                                <span class="badge bg-info">Dikonfirmasi</span>
                                            @elseif($booking->status === 'ongoing')
                                                <span class="badge bg-primary">Berlangsung</span>
                                            @elseif($booking->status === 'completed')
                                                <span class="badge bg-success">Selesai</span>
                                            @else
                                                <span class="badge bg-danger">Dibatalkan</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center mb-0">Belum ada riwayat booking</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Actions -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Aksi</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.cars.edit', $car->id) }}" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Edit Mobil
                    </a>
                    
                    @if($car->status !== 'rented')
                        <form method="POST" action="{{ route('admin.cars.toggleStatus', $car->id) }}">
                            @csrf
                            <button type="submit" class="btn btn-secondary w-100">
                                <i class="bi bi-arrow-repeat"></i> 
                                @if($car->status === 'available')
                                    Set Maintenance
                                @else
                                    Set Tersedia
                                @endif
                            </button>
                        </form>
                    @endif

                    <hr>

                    <form method="POST" 
                          action="{{ route('admin.cars.destroy', $car->id) }}" 
                          onsubmit="return confirm('Yakin ingin menghapus mobil ini? Data tidak dapat dikembalikan!')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="bi bi-trash"></i> Hapus Mobil
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Statistik</h5>
            </div>
            <div class="card-body">
                @php
                    $totalBookings = $car->bookings()->count();
                    $completedBookings = $car->bookings()->where('status', 'completed')->count();
                    $totalRevenue = $car->bookings()->where('payment_status', 'paid')->sum('total_price');
                @endphp

                <div class="d-flex justify-content-between mb-2">
                    <span>Total Booking</span>
                    <strong>{{ $totalBookings }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Booking Selesai</span>
                    <strong>{{ $completedBookings }}</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Total Revenue</span>
                    <strong class="text-success">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</strong>
                </div>
            </div>
        </div>

        <!-- Meta Info -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Info</h5>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <small class="text-muted">ID Mobil</small>
                    <p class="mb-0">#{{ $car->id }}</p>
                </div>
                <div class="mb-2">
                    <small class="text-muted">Ditambahkan</small>
                    <p class="mb-0">{{ $car->created_at->format('d M Y H:i') }}</p>
                </div>
                <div>
                    <small class="text-muted">Update Terakhir</small>
                    <p class="mb-0">{{ $car->updated_at->format('d M Y H:i') }}</p>
                </div>
            </div>
        </div>

        <a href="{{ route('admin.cars.index') }}" class="btn btn-outline-primary w-100 mt-3">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar
        </a>
    </div>
</div>
@endsection
