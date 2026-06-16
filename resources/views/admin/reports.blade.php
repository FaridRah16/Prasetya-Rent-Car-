@extends('layouts.admin')

@section('title', 'Laporan - Admin')
@section('page-title', 'Laporan')

@section('content')

<!-- Summary Stats -->
<div class="row">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stat-card bg-blue position-relative">
            <h3>{{ $totalBookings }}</h3>
            <p>Total Booking</p>
            <i class="bi bi-calendar-check"></i>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stat-card bg-green position-relative">
            <h3>{{ $completedBookings }}</h3>
            <p>Booking Selesai</p>
            <i class="bi bi-check-circle"></i>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stat-card bg-orange position-relative">
            <h3>Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
            <p>Total Revenue</p>
            <i class="bi bi-cash"></i>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stat-card bg-red position-relative">
            <h3>Rp {{ number_format($monthRevenue, 0, ',', '.') }}</h3>
            <p>Revenue Bulan Ini</p>
            <i class="bi bi-graph-up"></i>
        </div>
    </div>
</div>

<div class="row mt-4">
    <!-- Monthly Stats Table -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-bar-chart"></i> Statistik Bulanan (6 Bulan Terakhir)</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Bulan</th>
                                <th class="text-center">Jumlah Booking</th>
                                <th class="text-end">Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($monthlyStats as $stat)
                                <tr>
                                    <td>{{ $stat['month'] }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-primary">{{ $stat['bookings'] }}</span>
                                    </td>
                                    <td class="text-end">Rp {{ number_format($stat['revenue'], 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="fw-bold">
                                <td>Total</td>
                                <td class="text-center">{{ collect($monthlyStats)->sum('bookings') }}</td>
                                <td class="text-end">Rp {{ number_format(collect($monthlyStats)->sum('revenue'), 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Side Cards -->
    <div class="col-lg-4 mb-4">
        <!-- Booking Status Breakdown -->
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-pie-chart"></i> Status Booking</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span><i class="bi bi-clock text-warning"></i> Pending</span>
                    <strong>{{ $pendingBookings }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span><i class="bi bi-check-circle text-info"></i> Confirmed</span>
                    <strong>{{ $confirmedBookings }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span><i class="bi bi-car-front text-primary"></i> Ongoing</span>
                    <strong>{{ $ongoingBookings }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span><i class="bi bi-check-all text-success"></i> Completed</span>
                    <strong>{{ $completedBookings }}</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span><i class="bi bi-x-circle text-danger"></i> Cancelled</span>
                    <strong>{{ $cancelledBookings }}</strong>
                </div>
            </div>
        </div>

        <!-- Resource Summary -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-diagram-3"></i> Ringkasan</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span><i class="bi bi-car-front text-primary"></i> Total Mobil</span>
                    <strong>{{ $totalCars }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span><i class="bi bi-people text-info"></i> Total Customer</span>
                    <strong>{{ $totalCustomers }}</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span><i class="bi bi-person-badge text-success"></i> Total Driver</span>
                    <strong>{{ $totalDrivers }}</strong>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Top Cars -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-trophy"></i> Mobil Paling Populer</h5>
            </div>
            <div class="card-body">
                @if($topCars->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama Mobil</th>
                                    <th>Brand</th>
                                    <th>Tipe</th>
                                    <th class="text-center">Total Booking</th>
                                    <th class="text-end">Harga/Hari</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topCars as $index => $car)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td class="fw-bold">{{ $car->name }}</td>
                                        <td>{{ $car->brand }}</td>
                                        <td>{{ $car->type }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-primary">{{ $car->bookings_count }}</span>
                                        </td>
                                        <td class="text-end">Rp {{ number_format($car->price_per_day, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center mb-0">Belum ada data booking</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
