@extends('layouts.app')

@section('title', 'Beranda - Prasetya Rent Car')

@section('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap');
    
    * {
        font-family: 'Poppins', sans-serif;
    }
    
    .hero-section {
        background: linear-gradient(135deg, var(--primary-color) 0%, #2c5282 100%);
        position: relative;
        overflow: hidden;
        padding: 100px 0;
    }
    
    .hero-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse"><path d="M 40 0 L 0 0 0 40" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
        opacity: 0.5;
    }
    
    .hero-content {
        position: relative;
        z-index: 2;
    }
    
    .hero-title {
        font-size: 3.5rem;
        font-weight: 800;
        line-height: 1.2;
        margin-bottom: 1.5rem;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
    }
    
    .hero-subtitle {
        font-size: 1.25rem;
        font-weight: 300;
        opacity: 0.95;
        line-height: 1.6;
    }
    
    .hero-car-icon {
        font-size: 20rem;
        opacity: 0.1;
        position: absolute;
        right: -50px;
        top: 50%;
        transform: translateY(-50%);
    }
    
    .feature-card {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border: none;
        background: white;
        height: 100%;
    }
    
    .feature-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0,0,0,.15) !important;
    }
    
    .feature-icon {
        width: 70px;
        height: 70px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        margin: 0 auto 1.5rem;
        font-size: 2rem;
    }
    
    .section-title {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--primary-color);
        position: relative;
        display: inline-block;
    }
    
    .section-title::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 60px;
        height: 4px;
        background: var(--secondary-color);
        border-radius: 2px;
    }
    
    .car-card {
        border: none;
        border-radius: 15px;
        overflow: hidden;
        transition: all 0.4s;
        background: white;
        box-shadow: 0 5px 15px rgba(0,0,0,.08);
    }
    
    .car-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 35px rgba(0,0,0,.15);
    }
    
    .car-image-wrapper {
        height: 220px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }
    
    .car-image-wrapper::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        animation: pulse 3s ease-in-out infinite;
    }
    
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.1); }
    }
    
    .car-image-wrapper i {
        font-size: 6rem;
        color: white;
        position: relative;
        z-index: 1;
    }
    
    .car-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: var(--secondary-color);
        color: white;
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        z-index: 2;
    }
    
    .car-specs {
        display: flex;
        gap: 1rem;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #e9ecef;
    }
    
    .car-spec-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #6c757d;
        font-size: 0.9rem;
    }
    
    .price-tag {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--primary-color);
    }
    
    .cta-section {
        background: linear-gradient(135deg, var(--secondary-color) 0%, #d9901f 100%);
        position: relative;
        overflow: hidden;
    }
    
    .cta-section::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 500px;
        height: 500px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
    }
    
    @media (max-width: 768px) {
        .hero-title {
            font-size: 2.5rem;
        }
        
        .hero-car-icon {
            font-size: 15rem;
            right: -100px;
        }
    }
</style>
@endsection

@section('content')
<!-- Hero Section -->
<section class="hero-section text-white position-relative">
    <div class="container hero-content">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <h1 class="hero-title">
                    Rental Mobil<br>
                    <span style="color: var(--secondary-color);">Terpercaya</span> & Terjangkau
                </h1>
                <p class="hero-subtitle mb-5">
                    Nikmati perjalanan nyaman dengan armada terlengkap dan layanan terbaik. 
                    Prasetya Rent Car siap menemani setiap momen perjalanan Anda.
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="{{ route('cars.index') }}" class="btn btn-warning btn-lg px-5 py-3">
                        <i class="bi bi-car-front"></i> Lihat Katalog Mobil
                    </a>
                    @guest
                        <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg px-5 py-3">
                            <i class="bi bi-person-plus"></i> Daftar Gratis
                        </a>
                    @endguest
                </div>
            </div>
            <div class="col-lg-5 d-none d-lg-block">
                <i class="bi bi-car-front-fill hero-car-icon"></i>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-5 bg-light">
    <div class="container py-4">
        <div class="text-center mb-5">
            <h2 class="section-title mb-4">Mengapa Memilih Kami?</h2>
            <p class="text-muted">Keunggulan layanan yang kami tawarkan untuk kenyamanan Anda</p>
        </div>
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="card feature-card shadow-sm p-4">
                    <div class="card-body text-center">
                        <div class="feature-icon bg-primary bg-opacity-10">
                            <i class="bi bi-shield-check text-primary"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Armada Terawat</h5>
                        <p class="text-muted mb-0">Semua kendaraan dalam kondisi prima dan terawat dengan baik untuk kenyamanan maksimal perjalanan Anda</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card feature-card shadow-sm p-4">
                    <div class="card-body text-center">
                        <div class="feature-icon" style="background: rgba(245, 166, 35, 0.1);">
                            <i class="bi bi-cash-coin" style="color: var(--secondary-color);"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Harga Terjangkau</h5>
                        <p class="text-muted mb-0">Tarif kompetitif dengan berbagai paket hemat dan promo menarik setiap bulannya</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card feature-card shadow-sm p-4">
                    <div class="card-body text-center">
                        <div class="feature-icon bg-success bg-opacity-10">
                            <i class="bi bi-headset text-success"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Layanan 24/7</h5>
                        <p class="text-muted mb-0">Customer service profesional siap membantu Anda kapan saja, di mana saja</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Cars Section -->
<section class="py-5">
    <div class="container py-4">
        <div class="text-center mb-5">
            <h2 class="section-title mb-4">Mobil Pilihan Kami</h2>
            <p class="text-muted">Jelajahi koleksi mobil terbaik untuk berbagai kebutuhan perjalanan Anda</p>
        </div>

        @php
            $featuredCars = \App\Models\Car::where('status', 'available')
                ->orderBy('created_at', 'desc')
                ->take(6)
                ->get();
        @endphp

        @if($featuredCars->count() > 0)
            <div class="row g-4 mb-5">
                @foreach($featuredCars as $car)
                    <div class="col-lg-4 col-md-6">
                        <div class="car-card">
                            <div class="car-image-wrapper">
                                <span class="car-badge">Tersedia</span>
                                <i class="bi bi-car-front-fill"></i>
                            </div>
                            <div class="card-body p-4">
                                <h5 class="card-title fw-bold mb-2">{{ $car->name }}</h5>
                                <p class="text-muted mb-0">
                                    <i class="bi bi-tag-fill"></i> {{ $car->brand }} • {{ $car->type }}
                                </p>
                                
                                <div class="car-specs">
                                    <span class="car-spec-item">
                                        <i class="bi bi-people-fill"></i> {{ $car->seats }} Kursi
                                    </span>
                                    <span class="car-spec-item">
                                        <i class="bi bi-calendar3"></i> {{ $car->year }}
                                    </span>
                                    <span class="car-spec-item">
                                        <i class="bi bi-palette-fill"></i> {{ $car->color }}
                                    </span>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mt-4">
                                    <div>
                                        <div class="price-tag">Rp {{ number_format($car->price_per_day, 0, ',', '.') }}</div>
                                        <small class="text-muted">per hari</small>
                                    </div>
                                    <a href="{{ route('cars.show', $car->id) }}" class="btn btn-primary">
                                        <i class="bi bi-arrow-right"></i> Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="text-center">
                <a href="{{ route('cars.index') }}" class="btn btn-outline-primary btn-lg px-5">
                    <i class="bi bi-grid-3x3-gap"></i> Lihat Semua Mobil
                </a>
            </div>
        @else
            <div class="alert alert-info text-center">
                <i class="bi bi-info-circle"></i> Belum ada mobil yang tersedia saat ini.
            </div>
        @endif
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section py-5 text-white position-relative">
    <div class="container text-center py-4 position-relative" style="z-index: 2;">
        <h2 class="fw-bold mb-3" style="font-size: 2.5rem;">Siap Memulai Perjalanan Anda?</h2>
        <p class="mb-4" style="font-size: 1.15rem; opacity: 0.95;">
            Bergabunglah dengan ribuan pelanggan yang puas menggunakan layanan kami
        </p>
        @guest
            <a href="{{ route('register') }}" class="btn btn-light btn-lg px-5 py-3 me-2">
                <i class="bi bi-person-plus"></i> Daftar Sekarang
            </a>
            <a href="{{ route('cars.index') }}" class="btn btn-outline-light btn-lg px-5 py-3">
                <i class="bi bi-car-front"></i> Lihat Katalog
            </a>
        @else
            <a href="{{ route('customer.bookings.create') }}" class="btn btn-light btn-lg px-5 py-3">
                <i class="bi bi-calendar-check"></i> Mulai Booking Sekarang
            </a>
        @endguest
    </div>
</section>
@endsection
