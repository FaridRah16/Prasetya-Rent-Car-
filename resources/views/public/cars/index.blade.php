@extends('layouts.app')

@section('title', 'Katalog Mobil')

@section('styles')
<style>
    .catalog-header {
        background: linear-gradient(135deg, #111827 0%, #1e293b 100%);
        padding: 4rem 0;
        color: #ffffff;
        position: relative;
        overflow: hidden;
    }
    
    .catalog-header::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 15px;
        background: #ffffff;
        border-radius: 20px 20px 0 0;
    }

    .filter-card {
        background: #ffffff;
        border-radius: 20px;
        padding: 1.8rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.04);
        margin-top: -2.5rem;
        position: relative;
        z-index: 10;
        border: 1px solid #f1f5f9;
    }
    
    .form-control-custom, .form-select-custom {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 18px;
        font-weight: 500;
        color: #4a5568;
        background-color: #f8fafc;
        transition: all 0.3s ease;
    }
    
    .form-control-custom:focus, .form-select-custom:focus {
        border-color: #e30613;
        background-color: #ffffff;
        box-shadow: 0 0 0 3px rgba(227, 6, 19, 0.1);
        outline: none;
    }
    
    .btn-search-custom {
        background-color: #e30613;
        color: #ffffff;
        border: none;
        border-radius: 12px;
        padding: 12px 24px;
        font-weight: 700;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    
    .btn-search-custom:hover {
        background-color: #c40510;
        color: #ffffff;
    }

    .btn-reset-custom {
        background-color: #ffffff;
        color: #64748b;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 24px;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    
    .btn-reset-custom:hover {
        background-color: #f1f5f9;
        color: #334155;
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

    .animated-card {
        opacity: 0;
        animation: fadeInUp 0.7s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    .delay-1 { animation-delay: 0.05s; }
    .delay-2 { animation-delay: 0.1s; }
    .delay-3 { animation-delay: 0.15s; }
    .delay-4 { animation-delay: 0.2s; }
    .delay-5 { animation-delay: 0.25s; }
    .delay-6 { animation-delay: 0.3s; }
</style>
@endsection

@section('content')
<!-- Header Section -->
<div class="catalog-header">
    <div class="container text-center py-4">
        <h1 class="fw-bold mb-2 text-white">Katalog Mobil</h1>
        <p class="text-white-50 mb-0">Temukan kendaraan impian terbaik dengan kualitas prima dan harga sewa bersaing.</p>
    </div>
</div>

<div class="container mb-5">
    <!-- Filter Section -->
    <div class="filter-card animated-card">
        <form method="GET" action="{{ route('cars.index') }}">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label text-muted small fw-bold text-uppercase">Cari Mobil</label>
                    <input type="text" 
                           class="form-control form-control-custom" 
                           name="search" 
                           placeholder="Ketik nama mobil..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label text-muted small fw-bold text-uppercase">Merek</label>
                    <select class="form-select form-select-custom" name="brand">
                        <option value="">Semua Brand</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand }}" {{ request('brand') == $brand ? 'selected' : '' }}>
                                {{ $brand }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-muted small fw-bold text-uppercase">Tipe</label>
                    <select class="form-select form-select-custom" name="type">
                        <option value="">Semua Tipe</option>
                        @foreach($types as $type)
                            <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                                {{ $type }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-muted small fw-bold text-uppercase">Urutkan</label>
                    <select class="form-select form-select-custom" name="sort">
                        <option value="">Pilih Urutan</option>
                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>
                            Harga Terendah
                        </option>
                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>
                            Harga Tertinggi
                        </option>
                    </select>
                </div>
                <div class="col-md-3 d-flex flex-column justify-content-end">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn-search-custom flex-grow-1">
                            <i class="bi bi-search"></i> Cari
                        </button>
                        @if(request()->anyFilled(['search', 'brand', 'type', 'sort']))
                            <a href="{{ route('cars.index') }}" class="btn-reset-custom">
                                <i class="bi bi-x-lg"></i> Reset
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Cars Grid -->
    <div class="my-5">
        @if($cars->count() > 0)
            <div class="row g-4">
                @foreach($cars as $index => $car)
                    <div class="col-lg-4 col-md-6 animated-card delay-{{ ($index % 6) + 1 }}">
                        <div class="card car-display-card">
                            <div class="position-relative">
                                <div class="image-wrapper">
                                    <img src="{{ $car->image_url }}" alt="{{ $car->name }}">
                                </div>
                                @if($car->status === 'rented')
                                    @php $rentedUntil = optional($car->activeBooking)->end_date; @endphp
                                    <span class="badge bg-warning text-dark" style="position: absolute; top: 0; right: 0; border-radius: 30px; padding: 6px 14px; font-weight: 600; font-size: 0.75rem; box-shadow: 0 4px 10px rgba(255,193,7,0.25);">
                                        {{ $rentedUntil ? 'Disewa s/d ' . $rentedUntil->format('d M Y') : 'Sedang Disewa' }}
                                    </span>
                                @else
                                    <span class="badge bg-success" style="position: absolute; top: 0; right: 0; border-radius: 30px; padding: 6px 14px; font-weight: 600; font-size: 0.75rem; box-shadow: 0 4px 10px rgba(40,167,69,0.15);">Tersedia</span>
                                @endif
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
                                            <span class="spec-value">{{ strtoupper($car->transmission ?? (in_array(strtolower($car->type), ['suv', 'mpv']) ? 'AUTOMATIC' : 'MANUAL/AUTO')) }}</span>
                                        </div>
                                    </div>
                                    <!-- BAHAN BAKAR -->
                                    <div class="spec-item-custom">
                                        <div class="spec-icon-box">
                                            <i class="bi bi-fuel-pump-fill"></i>
                                        </div>
                                        <div class="spec-text-details">
                                            <span class="spec-label">Bahan Bakar</span>
                                            <span class="spec-value">{{ strtoupper($car->fuel ?? (str_contains(strtolower($car->name), 'innova') ? 'DIESEL' : 'BENSIN')) }}</span>
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

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-5">
                {{ $cars->links() }}
            </div>
        @else
            <div class="alert alert-info text-center py-4 rounded-4" style="border: none; background-color: #f0f7ff; color: #0056b3;">
                <i class="bi bi-info-circle-fill fs-3 mb-2 d-block"></i>
                <span class="fw-semibold">Tidak ada mobil yang ditemukan. Silakan coba filter lain.</span>
            </div>
        @endif
    </div>
</div>
@endsection
