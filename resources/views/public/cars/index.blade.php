@extends('layouts.app')

@section('title', 'Katalog Mobil')

@section('styles')
<style>
    .filter-section {
        background: white;
        padding: 1.5rem;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,.1);
        margin-bottom: 2rem;
    }
    
    .car-card {
        height: 100%;
        transition: all 0.3s;
    }
    
    .car-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,.15);
    }
    
    .car-image {
        height: 200px;
        object-fit: cover;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 4rem;
    }
    
    .price-tag {
        font-size: 1.5rem;
        font-weight: bold;
        color: var(--primary-color);
    }
    
    .badge-status {
        position: absolute;
        top: 10px;
        right: 10px;
    }
</style>
@endsection

@section('content')
<div class="bg-primary text-white py-4">
    <div class="container">
        <h2 class="mb-0"><i class="bi bi-car-front"></i> Katalog Mobil</h2>
        <p class="mb-0">Pilih mobil terbaik untuk perjalanan Anda</p>
    </div>
</div>

<div class="container my-5">
    <!-- Filter Section -->
    <div class="filter-section">
        <form method="GET" action="{{ route('cars.index') }}">
            <div class="row g-3">
                <div class="col-md-3">
                    <input type="text" 
                           class="form-control" 
                           name="search" 
                           placeholder="Cari mobil..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="brand">
                        <option value="">Semua Brand</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand }}" {{ request('brand') == $brand ? 'selected' : '' }}>
                                {{ $brand }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="type">
                        <option value="">Semua Tipe</option>
                        @foreach($types as $type)
                            <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                                {{ $type }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="sort">
                        <option value="">Urutkan</option>
                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>
                            Harga Terendah
                        </option>
                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>
                            Harga Tertinggi
                        </option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Cari
                    </button>
                    @if(request()->anyFilled(['search', 'brand', 'type', 'sort']))
                        <a href="{{ route('cars.index') }}" class="btn btn-outline-secondary w-100 mt-2">
                            <i class="bi bi-x-circle"></i> Reset
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Cars Grid -->
    @if($cars->count() > 0)
        <div class="row g-4">
            @foreach($cars as $car)
                <div class="col-lg-4 col-md-6">
                    <div class="card car-card">
                        <div class="position-relative">
                            <div class="car-image">
                                <i class="bi bi-car-front-fill"></i>
                            </div>
                            <span class="badge bg-success badge-status">Tersedia</span>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">{{ $car->name }}</h5>
                            <p class="text-muted mb-2">
                                <i class="bi bi-tag"></i> {{ $car->brand }} • {{ $car->type }}
                            </p>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted">
                                    <i class="bi bi-people"></i> {{ $car->seats }} Kursi
                                </span>
                                <span class="text-muted">
                                    <i class="bi bi-calendar3"></i> {{ $car->year }}
                                </span>
                                <span class="text-muted">
                                    <i class="bi bi-palette"></i> {{ $car->color }}
                                </span>
                            </div>
                            <div class="price-tag mb-3">
                                Rp {{ number_format($car->price_per_day, 0, ',', '.') }}
                                <small class="text-muted" style="font-size: 0.9rem;">/hari</small>
                            </div>
                            <a href="{{ route('cars.show', $car->id) }}" class="btn btn-warning w-100">
                                <i class="bi bi-eye"></i> Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $cars->links() }}
        </div>
    @else
        <div class="alert alert-info text-center">
            <i class="bi bi-info-circle"></i> Tidak ada mobil yang ditemukan. Silakan coba filter lain.
        </div>
    @endif
</div>
@endsection
