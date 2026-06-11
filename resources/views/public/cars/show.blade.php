@extends('layouts.app')

@section('title', $car->name)

@section('styles')
<style>
    .car-detail-hero {
        background: linear-gradient(135deg, var(--primary-color) 0%, #2c5282 100%);
        color: white;
        padding: 3rem 0;
    }
    
    .car-main-image {
        height: 400px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 8rem;
        box-shadow: 0 10px 30px rgba(0,0,0,.2);
    }
    
    .spec-item {
        padding: 1rem;
        background: white;
        border-radius: 10px;
        text-align: center;
        box-shadow: 0 2px 10px rgba(0,0,0,.1);
        transition: all 0.3s;
    }
    
    .spec-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,.15);
    }
    
    .spec-item i {
        font-size: 2rem;
        color: var(--primary-color);
        margin-bottom: 0.5rem;
    }
    
    .price-box {
        background: var(--secondary-color);
        color: white;
        padding: 2rem;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,.2);
    }
    
    .price-box h2 {
        font-size: 2.5rem;
        font-weight: bold;
        margin-bottom: 0.5rem;
    }
</style>
@endsection

@section('content')
<!-- Hero Section -->
<div class="car-detail-hero">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb text-white">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white">Beranda</a></li>
                <li class="breadcrumb-item"><a href="{{ route('cars.index') }}" class="text-white">Katalog</a></li>
                <li class="breadcrumb-item active text-white-50">{{ $car->name }}</li>
            </ol>
        </nav>
        <h1 class="display-5 fw-bold">{{ $car->name }}</h1>
        <p class="lead mb-0">
            <i class="bi bi-tag"></i> {{ $car->brand }} • {{ $car->type }} • {{ $car->year }}
        </p>
    </div>
</div>

<div class="container my-5">
    <div class="row">
        <!-- Left Column: Car Image & Description -->
        <div class="col-lg-8 mb-4">
            <!-- Car Image -->
            <div class="car-main-image mb-4">
                <i class="bi bi-car-front-fill"></i>
            </div>

            <!-- Specifications -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-clipboard-data"></i> Spesifikasi</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3 col-6">
                            <div class="spec-item">
                                <i class="bi bi-people-fill"></i>
                                <p class="mb-0 fw-bold">{{ $car->seats }} Kursi</p>
                                <small class="text-muted">Kapasitas</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="spec-item">
                                <i class="bi bi-calendar3"></i>
                                <p class="mb-0 fw-bold">{{ $car->year }}</p>
                                <small class="text-muted">Tahun</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="spec-item">
                                <i class="bi bi-palette-fill"></i>
                                <p class="mb-0 fw-bold">{{ $car->color }}</p>
                                <small class="text-muted">Warna</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="spec-item">
                                <i class="bi bi-card-text"></i>
                                <p class="mb-0 fw-bold">{{ $car->plate_number }}</p>
                                <small class="text-muted">No Plat</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Deskripsi</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $car->description ?? 'Mobil berkualitas dengan performa terbaik untuk perjalanan Anda.' }}</p>
                </div>
            </div>
        </div>

        <!-- Right Column: Booking Info -->
        <div class="col-lg-4">
            <!-- Price & Book Button -->
            <div class="price-box mb-4">
                <h5 class="text-white-50 mb-2">Harga Sewa</h5>
                <h2>Rp {{ number_format($car->price_per_day, 0, ',', '.') }}</h2>
                <p class="mb-4">per hari</p>

                @if($car->status === 'available')
                    <div class="d-grid gap-2">
                        @auth
                            @if(Auth::user()->isCustomer())
                                <a href="{{ route('customer.bookings.create', ['car_id' => $car->id]) }}" class="btn btn-light btn-lg">
                                    <i class="bi bi-calendar-check"></i> Sewa Sekarang
                                </a>
                            @else
                                <a href="{{ route('customer.bookings.create', ['car_id' => $car->id]) }}" class="btn btn-light btn-lg">
                                    <i class="bi bi-calendar-check"></i> Sewa Sekarang
                                </a>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="btn btn-light btn-lg">
                                <i class="bi bi-box-arrow-in-right"></i> Login untuk Sewa
                            </a>
                            <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg">
                                <i class="bi bi-person-plus"></i> Daftar
                            </a>
                        @endauth
                    </div>
                @else
                    <div class="alert alert-warning mb-0">
                        <i class="bi bi-exclamation-triangle"></i> Mobil sedang tidak tersedia
                    </div>
                @endif
            </div>

            <!-- Features -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-check-circle"></i> Keuntungan Sewa</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="bi bi-check-circle-fill text-success"></i> Mobil terawat & bersih
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle-fill text-success"></i> Asuransi terlindungi
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle-fill text-success"></i> Driver profesional (opsional)
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle-fill text-success"></i> Bahan bakar penuh
                        </li>
                        <li class="mb-0">
                            <i class="bi bi-check-circle-fill text-success"></i> Support 24/7
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="card mt-3">
                <div class="card-body text-center">
                    <h6><i class="bi bi-headset"></i> Butuh Bantuan?</h6>
                    <p class="mb-2 text-muted">Hubungi kami:</p>
                    <a href="tel:+6281234567890" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-telephone"></i> +62 812-3456-7890
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- More Cars -->
    <div class="mt-5">
        <h4 class="mb-4">Mobil Lainnya</h4>
        <div class="row g-4">
            @php
                $otherCars = \App\Models\Car::where('id', '!=', $car->id)
                    ->where('status', 'available')
                    ->take(3)
                    ->get();
            @endphp

            @foreach($otherCars as $otherCar)
                <div class="col-md-4">
                    <div class="card h-100">
                        <div style="height: 150px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);" class="d-flex align-items-center justify-content-center text-white">
                            <i class="bi bi-car-front-fill" style="font-size: 4rem;"></i>
                        </div>
                        <div class="card-body">
                            <h6 class="card-title">{{ $otherCar->name }}</h6>
                            <p class="text-muted mb-2 small">{{ $otherCar->brand }} • {{ $otherCar->type }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-primary">
                                    Rp {{ number_format($otherCar->price_per_day, 0, ',', '.') }}/hari
                                </span>
                                <a href="{{ route('cars.show', $otherCar->id) }}" class="btn btn-sm btn-outline-warning">
                                    Lihat
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
