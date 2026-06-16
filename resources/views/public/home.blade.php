@extends('layouts.app')

@section('title', 'Beranda - Prasetya Rent Car')

@section('styles')
<style>
    .hero-section {
        background: linear-gradient(135deg, #111827 0%, #1e293b 100%);
        position: relative;
        overflow: hidden;
        padding: 100px 0;
        color: #ffffff;
    }
    
    .hero-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse"><path d="M 40 0 L 0 0 0 40" fill="none" stroke="rgba(255,255,255,0.03)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
        opacity: 0.5;
    }
    
    .hero-content {
        position: relative;
        z-index: 2;
    }
    
    .hero-title {
        font-size: 3.5rem;
        font-weight: 850;
        line-height: 1.2;
        margin-bottom: 1.5rem;
    }
    
    .hero-subtitle {
        font-size: 1.25rem;
        font-weight: 400;
        opacity: 0.85;
        line-height: 1.7;
    }
    
    .hero-car-icon {
        font-size: 20rem;
        opacity: 0.08;
        position: absolute;
        right: -50px;
        top: 50%;
        transform: translateY(-50%);
        color: #e30613;
    }
    
    .feature-card {
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        border: none;
        background: #ffffff;
        border-radius: 20px;
        padding: 2.2rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
        height: 100%;
        border: 1px solid #f1f5f9;
    }
    
    .feature-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.08) !important;
        border-color: #e2e8f0;
    }
    
    .feature-icon {
        width: 70px;
        height: 70px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 16px;
        margin-bottom: 1.5rem;
        font-size: 2rem;
    }
    
    .section-title {
        font-size: 2.3rem;
        font-weight: 800;
        color: #0f172a;
        position: relative;
        display: inline-block;
        margin-bottom: 1.5rem;
    }
    
    .section-title::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 50px;
        height: 4px;
        background: #e30613;
        border-radius: 2px;
    }

    .car-display-card {
        background: #ffffff;
        border: none;
        border-radius: 20px;
        padding: 24px;
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.02);
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
        border: 1px solid #f8fafc;
    }

    .car-display-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
        border-color: #f1f5f9;
    }

    .car-display-card .image-wrapper {
        width: 100%;
        height: 180px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.5rem;
        background: #ffffff;
        border-radius: 16px;
        overflow: hidden;
    }

    .car-display-card .image-wrapper img {
        max-width: 95%;
        max-height: 90%;
        object-fit: contain;
        filter: drop-shadow(0 12px 8px rgba(0, 0, 0, 0.12));
        transition: transform 0.4s ease;
    }

    .car-display-card:hover .image-wrapper img {
        transform: scale(1.06);
    }

    .car-name-title {
        font-weight: 800;
        font-size: 1.5rem;
        color: #0f172a;
    }

    .specs-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px 12px;
        margin-bottom: 1.5rem;
    }

    .spec-item-custom {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .spec-icon-box {
        width: 36px;
        height: 36px;
        border: 1.5px solid #0056b3;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #0056b3;
        font-size: 1.1rem;
        flex-shrink: 0;
        background-color: #f0f7ff;
    }

    .spec-text-details {
        display: flex;
        flex-direction: column;
    }

    .spec-text-details .spec-label {
        font-size: 0.65rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #94a3b8;
        letter-spacing: 0.5px;
        margin-bottom: 1px;
    }

    .spec-text-details .spec-value {
        font-size: 0.78rem;
        font-weight: 800;
        color: #1e293b;
        text-transform: uppercase;
    }

    .car-footer-action {
        border-top: 1px solid #f1f5f9;
        padding-top: 1.2rem;
        margin-top: auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
    }

    .car-price-info {
        display: flex;
        flex-direction: column;
    }

    .car-price-info .price-amount {
        font-size: 1.3rem;
        font-weight: 800;
        color: #e30613;
        line-height: 1.1;
    }

    .car-price-info .price-label {
        font-size: 0.75rem;
        color: #64748b;
        font-weight: 600;
    }

    .btn-details-custom {
        background-color: #e30613;
        color: #ffffff;
        border: none;
        border-radius: 30px;
        padding: 8px 20px;
        font-weight: 700;
        font-size: 0.85rem;
        transition: all 0.2s ease;
        box-shadow: 0 4px 10px rgba(227, 6, 19, 0.15);
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .btn-details-custom:hover {
        background-color: #c40510;
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 6px 15px rgba(227, 6, 19, 0.25);
    }
    
    .cta-section {
        background: linear-gradient(135deg, #e30613 0%, #ab040e 100%);
        position: relative;
        overflow: hidden;
        color: #ffffff;
    }
    
    .cta-section::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 500px;
        height: 500px;
        background: rgba(255,255,255,0.06);
        border-radius: 50%;
    }

    .btn-hero-red {
        background-color: #e30613;
        color: #ffffff;
        border-radius: 12px;
        padding: 14px 30px;
        font-weight: 700;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 15px rgba(227, 6, 19, 0.3);
        text-decoration: none;
    }

    .btn-hero-red:hover {
        background-color: #c40510;
        color: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(227, 6, 19, 0.4);
    }

    .btn-hero-outline {
        border: 2px solid #ffffff;
        color: #ffffff;
        border-radius: 12px;
        padding: 14px 30px;
        font-weight: 700;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }

    .btn-hero-outline:hover {
        background-color: #ffffff;
        color: #111827;
        transform: translateY(-2px);
    }

    /* Animation on Entrance */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(24px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animated-item {
        opacity: 0;
        animation: fadeInUp 0.7s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    .delay-1 { animation-delay: 0.05s; }
    .delay-2 { animation-delay: 0.1s; }
    .delay-3 { animation-delay: 0.15s; }
    .delay-4 { animation-delay: 0.2s; }
    .delay-5 { animation-delay: 0.25s; }
    .delay-6 { animation-delay: 0.3s; }

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
<section class="hero-section position-relative">
    <div class="container hero-content animated-item delay-1">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <h1 class="hero-title text-white">
                    Rental Mobil<br>
                    <span style="color: #e30613;">Terpercaya</span> & Terjangkau
                </h1>
                <p class="hero-subtitle mb-5 text-white-50">
                    Nikmati perjalanan nyaman dengan armada terlengkap dan layanan terbaik. 
                    Prasetya Rent Car siap menemani setiap momen perjalanan Anda.
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="{{ route('cars.index') }}" class="btn-hero-red">
                        <i class="bi bi-car-front"></i> Lihat Katalog Mobil
                    </a>
                    @guest
                        <a href="{{ route('register') }}" class="btn-hero-outline">
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
        <div class="text-center mb-5 animated-item delay-2">
            <h2 class="section-title mb-4">Mengapa Memilih Kami?</h2>
            <p class="text-muted">Keunggulan layanan yang kami tawarkan untuk kenyamanan Anda</p>
        </div>
        <div class="row g-4">
            <div class="col-lg-4 col-md-6 animated-item delay-3">
                <div class="card feature-card p-4">
                    <div class="card-body text-center">
                        <div class="feature-icon" style="background-color: #f0f7ff; color: #0056b3;">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <h5 class="fw-bold mb-3 text-dark">Armada Terawat</h5>
                        <p class="text-muted mb-0 small" style="line-height: 1.6;">Semua kendaraan dalam kondisi prima dan terawat dengan baik untuk kenyamanan maksimal perjalanan Anda</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 animated-item delay-4">
                <div class="card feature-card p-4">
                    <div class="card-body text-center">
                        <div class="feature-icon" style="background-color: #fff5f5; color: #e30613;">
                            <i class="bi bi-cash-coin"></i>
                        </div>
                        <h5 class="fw-bold mb-3 text-dark">Harga Terjangkau</h5>
                        <p class="text-muted mb-0 small" style="line-height: 1.6;">Tarif kompetitif dengan berbagai paket hemat dan promo menarik setiap bulannya</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 animated-item delay-5">
                <div class="card feature-card p-4">
                    <div class="card-body text-center">
                        <div class="feature-icon" style="background-color: #f0fdf4; color: #15803d;">
                            <i class="bi bi-headset"></i>
                        </div>
                        <h5 class="fw-bold mb-3 text-dark">Layanan 24/7</h5>
                        <p class="text-muted mb-0 small" style="line-height: 1.6;">Customer service profesional siap membantu Anda kapan saja, di mana saja</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Cars Section -->
<section class="py-5 bg-white">
    <div class="container py-4">
        <div class="text-center mb-5 animated-item delay-2">
            <h2 class="section-title mb-4">Mobil Pilihan Kami</h2>
            <p class="text-muted">Jelajahi koleksi mobil terbaik untuk berbagai kebutuhan perjalanan Anda</p>
        </div>

        @if($featuredCars->count() > 0)
            <div class="row g-4 mb-5">
                @foreach($featuredCars as $index => $car)
                    <div class="col-lg-4 col-md-6 animated-item delay-{{ ($index % 3) + 3 }}">
                        <div class="card car-display-card">
                            <div class="position-relative">
                                <div class="image-wrapper">
                                    <img src="{{ $car->image_url }}" alt="{{ $car->name }}">
                                </div>
                                <span class="badge bg-success" style="position: absolute; top: 0; right: 0; border-radius: 30px; padding: 6px 14px; font-weight: 600; font-size: 0.75rem; box-shadow: 0 4px 10px rgba(40,167,69,0.15);">Tersedia</span>
                            </div>
                            <div class="mb-4">
                                <h4 class="car-name-title mb-1">{{ $car->name }}</h4>
                                <p class="text-muted small mb-4" style="font-weight: 500;">
                                    {{ $car->brand }} • {{ $car->type }}
                                </p>
                                
                                <div class="specs-grid">
                                    <!-- TEMPAT DUDUK -->
                                    <div class="spec-item-custom">
                                        <div class="spec-icon-box">
                                            <i class="bi bi-people-fill"></i>
                                        </div>
                                        <div class="spec-text-details">
                                            <span class="spec-label">Tempat Duduk</span>
                                            <span class="spec-value">{{ $car->seats }}</span>
                                        </div>
                                    </div>
                                    <!-- BAGASI -->
                                    <div class="spec-item-custom">
                                        <div class="spec-icon-box">
                                            <i class="bi bi-briefcase-fill"></i>
                                        </div>
                                        <div class="spec-text-details">
                                            <span class="spec-label">Bagasi</span>
                                            <span class="spec-value">{{ $car->seats > 5 ? '2' : '1' }}</span>
                                        </div>
                                    </div>
                                    <!-- TRANSMISI -->
                                    <div class="spec-item-custom">
                                        <div class="spec-icon-box">
                                            <i class="bi bi-gear-wide-connected"></i>
                                        </div>
                                        <div class="spec-text-details">
                                            <span class="spec-label">Transmisi</span>
                                            <span class="spec-value">{{ in_array(strtolower($car->type), ['suv', 'mpv']) ? 'AUTOMATIC' : 'MANUAL/AUTO' }}</span>
                                        </div>
                                    </div>
                                    <!-- BAHAN BAKAR -->
                                    <div class="spec-item-custom">
                                        <div class="spec-icon-box">
                                            <i class="bi bi-fuel-pump-fill"></i>
                                        </div>
                                        <div class="spec-text-details">
                                            <span class="spec-label">Bahan Bakar</span>
                                            <span class="spec-value">{{ str_contains(strtolower($car->name), 'innova') ? 'DIESEL' : 'BENSIN' }}</span>
                                        </div>
                                    </div>
                                    <!-- ASURANSI KENDARAAN -->
                                    <div class="spec-item-custom">
                                        <div class="spec-icon-box">
                                            <i class="bi bi-shield-fill-check"></i>
                                        </div>
                                        <div class="spec-text-details">
                                            <span class="spec-label">Asuransi</span>
                                            <span class="spec-value">YES</span>
                                        </div>
                                    </div>
                                    <!-- PENGEMUDI -->
                                    <div class="spec-item-custom">
                                        <div class="spec-icon-box">
                                            <i class="bi bi-steering"></i>
                                        </div>
                                        <div class="spec-text-details">
                                            <span class="spec-label">Pengemudi</span>
                                            <span class="spec-value">YES</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="car-footer-action">
                                <div class="car-price-info">
                                    <span class="price-amount">Rp {{ number_format($car->price_per_day, 0, ',', '.') }}</span>
                                    <span class="price-label">/ Hari</span>
                                </div>
                                <a href="{{ route('cars.show', $car->id) }}" class="btn-details-custom text-decoration-none">
                                    Lihat Detail <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="text-center animated-item delay-6">
                <a href="{{ route('cars.index') }}" class="btn btn-outline-danger rounded-pill px-5 py-3 fw-bold">
                    <i class="bi bi-grid-3x3-gap-fill"></i> Lihat Semua Mobil
                </a>
            </div>
        @else
            <div class="alert alert-info text-center py-4 rounded-4" style="border: none; background-color: #f0f7ff; color: #0056b3;">
                <i class="bi bi-info-circle-fill fs-3 mb-2 d-block"></i>
                <span class="fw-semibold">Belum ada mobil yang tersedia saat ini.</span>
            </div>
        @endif
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section py-5 position-relative">
    <div class="container text-center py-4 position-relative" style="z-index: 2;">
        <h2 class="fw-extrabold mb-3 text-white" style="font-size: 2.5rem;">Siap Memulai Perjalanan Anda?</h2>
        <p class="mb-5 text-white-50" style="font-size: 1.15rem;">
            Bergabunglah dengan ribuan pelanggan yang puas menggunakan layanan kami
        </p>
        @guest
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="{{ route('register') }}" class="btn btn-light rounded-pill px-5 py-3 fw-bold text-danger">
                    <i class="bi bi-person-plus-fill"></i> Daftar Sekarang
                </a>
                <a href="{{ route('cars.index') }}" class="btn btn-outline-light rounded-pill px-5 py-3 fw-bold">
                    <i class="bi bi-car-front-fill"></i> Lihat Katalog
                </a>
            </div>
        @else
            <a href="{{ route('customer.bookings.create') }}" class="btn btn-light rounded-pill px-5 py-3 fw-bold text-danger">
                <i class="bi bi-calendar-check-fill"></i> Mulai Booking Sekarang
            </a>
        @endguest
    </div>
</section>
@endsection
