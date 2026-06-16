@extends('layouts.customer')

@section('title', 'Dashboard Customer')
@section('page-title', 'Dashboard Customer')

@section('content')

<div class="row">
    <div class="col-md-4">
        <div class="stat-card position-relative" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <h3>{{ $totalBookings }}</h3>
            <p>Total Booking</p>
            <i class="bi bi-calendar-check"></i>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card position-relative" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <h3>{{ $activeBookings }}</h3>
            <p>Booking Aktif</p>
            <i class="bi bi-clock-history"></i>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card position-relative" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
            <h3>Rp {{ number_format($totalSpending, 0, ',', '.') }}</h3>
            <p>Total Pengeluaran</p>
            <i class="bi bi-cash"></i>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Selamat Datang, {{ Auth::user()->name }}!</h5>
                <a href="{{ route('customer.bookings.create') }}" class="btn btn-warning btn-sm">
                    <i class="bi bi-plus-circle"></i> Booking Baru
                </a>
            </div>
            <div class="card-body">
                <p>Mulai booking mobil impian Anda sekarang!</p>
                <a href="{{ route('cars.index') }}" class="btn btn-primary">
                    <i class="bi bi-car-front"></i> Lihat Katalog Mobil
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
