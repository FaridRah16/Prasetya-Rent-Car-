@extends('layouts.admin')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard Admin')

@section('content')

<div class="row">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card bg-blue position-relative">
            <h3>{{ $totalCars }}</h3>
            <p>Total Mobil ({{ $availableCars }} tersedia)</p>
            <i class="bi bi-car-front"></i>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card bg-green position-relative">
            <h3>{{ $todayBookings }}</h3>
            <p>Booking Hari Ini ({{ $pendingBookings }} pending)</p>
            <i class="bi bi-calendar-check"></i>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card bg-orange position-relative">
            <h3>{{ $totalUsers }}</h3>
            <p>Total User ({{ $totalCustomers }} customer)</p>
            <i class="bi bi-people"></i>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card bg-red position-relative">
            <h3>Rp {{ number_format($monthRevenue / 1000, 0) }}k</h3>
            <p>Revenue Bulan Ini</p>
            <i class="bi bi-cash"></i>
        </div>
    </div>
</div>

<div class="row mt-4">
    <!-- Recent Bookings -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Booking Terbaru</h5>
                <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-outline-primary">
                    Lihat Semua
                </a>
            </div>
            <div class="card-body">
                @if($recentBookings->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Customer</th>
                                    <th>Mobil</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentBookings as $booking)
                                    <tr>
                                        <td>#{{ $booking->id }}</td>
                                        <td>{{ $booking->user->name }}</td>
                                        <td>{{ $booking->car->name }}</td>
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
                                        <td>
                                            <a href="{{ route('admin.bookings.show', $booking->id) }}" class="btn btn-sm btn-outline-primary">
                                                Detail
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center mb-0">Belum ada booking</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions & Stats -->
    <div class="col-lg-4 mb-4">
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.bookings.index', ['status' => 'pending']) }}" class="btn btn-warning">
                        <i class="bi bi-clock"></i> Booking Pending ({{ $pendingBookings }})
                    </a>
                    <a href="{{ route('admin.cars.index') }}" class="btn btn-primary">
                        <i class="bi bi-car-front"></i> Kelola Mobil
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-info">
                        <i class="bi bi-people"></i> Kelola User
                    </a>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Status Mobil</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span><i class="bi bi-check-circle text-success"></i> Tersedia</span>
                    <strong>{{ $availableCars }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span><i class="bi bi-car-front text-primary"></i> Disewa</span>
                    <strong>{{ $rentedCars }}</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span><i class="bi bi-wrench text-warning"></i> Maintenance</span>
                    <strong>{{ $maintenanceCars }}</strong>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
