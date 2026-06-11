@extends('layouts.admin')

@section('title', 'Detail User')
@section('page-title', 'Detail User')

@section('styles')
<style>
    .info-card {
        border-left: 4px solid #1a3c5e;
    }
    .stat-card {
        transition: transform 0.3s;
    }
    .stat-card:hover {
        transform: translateY(-5px);
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-4">
        <!-- User Profile Card -->
        <div class="card info-card mb-4">
            <div class="card-body text-center">
                <div class="avatar-circle mx-auto mb-3" style="width: 100px; height: 100px; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-person-fill text-white" style="font-size: 3rem;"></i>
                </div>
                <h4 class="mb-1">{{ $user->name }}</h4>
                @if($user->role === 'admin')
                    <span class="badge bg-danger mb-2">
                        <i class="bi bi-shield-fill"></i> Admin
                    </span>
                @elseif($user->role === 'customer')
                    <span class="badge bg-primary mb-2">
                        <i class="bi bi-person-fill"></i> Customer
                    </span>
                @else
                    <span class="badge bg-info mb-2">
                        <i class="bi bi-car-front-fill"></i> Driver
                    </span>
                @endif
                
                <hr>
                
                <div class="text-start">
                    <p class="mb-2">
                        <i class="bi bi-envelope"></i> {{ $user->email }}
                    </p>
                    <p class="mb-2">
                        <i class="bi bi-telephone"></i> {{ $user->phone }}
                    </p>
                    <p class="mb-2">
                        <i class="bi bi-calendar3"></i> Terdaftar: {{ $user->created_at->format('d M Y') }}
                    </p>
                    @if($user->driver)
                        <p class="mb-0">
                            <i class="bi bi-card-text"></i> SIM: {{ $user->driver->license_number }}
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-gear"></i> Aksi</h5>
            </div>
            <div class="card-body d-grid gap-2">
                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-warning">
                    <i class="bi bi-pencil"></i> Edit User
                </a>
                @if($user->id !== auth()->id())
                    <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="bi bi-trash"></i> Hapus User
                        </button>
                    </form>
                @endif
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        @if($user->role === 'driver')
            <!-- Driver Statistics -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card stat-card bg-info text-white">
                        <div class="card-body">
                            <h3 class="mb-0">{{ $user->bookingsAsDriver()->whereIn('status', ['confirmed', 'ongoing'])->count() }}</h3>
                            <p class="mb-0">Tugas Aktif</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card bg-success text-white">
                        <div class="card-body">
                            <h3 class="mb-0">{{ $user->bookingsAsDriver()->where('status', 'completed')->count() }}</h3>
                            <p class="mb-0">Tugas Selesai</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card" style="background: {{ $user->driver->status === 'available' ? '#28a745' : '#dc3545' }}; color: white;">
                        <div class="card-body">
                            <h5 class="mb-0">
                                @if($user->driver->status === 'available')
                                    <i class="bi bi-check-circle"></i> Tersedia
                                @else
                                    <i class="bi bi-x-circle"></i> Bertugas
                                @endif
                            </h5>
                            <p class="mb-0">Status Driver</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Driver Task History -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-clock-history"></i> Riwayat Tugas Driver</h5>
                </div>
                <div class="card-body">
                    @if($user->bookingsAsDriver->isEmpty())
                        <div class="text-center py-4">
                            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2">Belum ada riwayat tugas</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Customer</th>
                                        <th>Mobil</th>
                                        <th>Tanggal</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($user->bookingsAsDriver->sortByDesc('created_at')->take(10) as $booking)
                                        <tr>
                                            <td>#{{ $booking->id }}</td>
                                            <td>{{ $booking->user->name }}</td>
                                            <td>{{ $booking->car->name }}</td>
                                            <td>{{ $booking->start_date->format('d M Y') }}</td>
                                            <td>
                                                @if($booking->status === 'pending')
                                                    <span class="badge bg-warning">Pending</span>
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
                                            <td>
                                                <a href="{{ route('admin.bookings.show', $booking->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        @elseif($user->role === 'customer')
            <!-- Customer Statistics -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card stat-card bg-primary text-white">
                        <div class="card-body">
                            <h3 class="mb-0">{{ $user->bookings->count() }}</h3>
                            <p class="mb-0">Total Booking</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card bg-warning text-dark">
                        <div class="card-body">
                            <h3 class="mb-0">{{ $user->bookings->where('status', 'pending')->count() }}</h3>
                            <p class="mb-0">Pending</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card bg-info text-white">
                        <div class="card-body">
                            <h3 class="mb-0">{{ $user->bookings->whereIn('status', ['confirmed', 'ongoing'])->count() }}</h3>
                            <p class="mb-0">Aktif</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card bg-success text-white">
                        <div class="card-body">
                            <h3 class="mb-0">{{ $user->bookings->where('status', 'completed')->count() }}</h3>
                            <p class="mb-0">Selesai</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Revenue Card -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="text-muted mb-1">Total Transaksi</h5>
                    <h2 class="text-primary mb-0">
                        Rp {{ number_format($user->bookings->where('payment_status', 'paid')->sum('total_price'), 0, ',', '.') }}
                    </h2>
                </div>
            </div>

            <!-- Customer Booking History -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-clock-history"></i> Riwayat Booking Customer</h5>
                </div>
                <div class="card-body">
                    @if($user->bookings->isEmpty())
                        <div class="text-center py-4">
                            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2">Belum ada riwayat booking</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Mobil</th>
                                        <th>Tanggal</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Pembayaran</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($user->bookings->sortByDesc('created_at')->take(10) as $booking)
                                        <tr>
                                            <td>#{{ $booking->id }}</td>
                                            <td>{{ $booking->car->name }}</td>
                                            <td>{{ $booking->start_date->format('d M Y') }}</td>
                                            <td>Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                                            <td>
                                                @if($booking->status === 'pending')
                                                    <span class="badge bg-warning">Pending</span>
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
                                            <td>
                                                @if($booking->payment_status === 'paid')
                                                    <span class="badge bg-success">Lunas</span>
                                                @else
                                                    <span class="badge bg-danger">Belum Bayar</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.bookings.show', $booking->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        @else
            <!-- Admin Info -->
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-shield-fill-check text-primary" style="font-size: 5rem;"></i>
                    <h3 class="mt-3">Administrator</h3>
                    <p class="text-muted">User ini memiliki akses penuh ke sistem</p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
