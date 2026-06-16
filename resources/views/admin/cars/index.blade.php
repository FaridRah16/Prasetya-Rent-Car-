@extends('layouts.admin')

@section('title', 'Kelola Mobil')
@section('page-title', 'Kelola Mobil')

@section('styles')
<style>
    .filter-card-admin {
        background: #ffffff;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.01), 0 2px 4px -1px rgba(0, 0, 0, 0.01);
        border: 1px solid #e2e8f0;
        margin-bottom: 2rem;
    }

    .form-control-custom, .form-select-custom {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 10px 16px;
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

    .btn-search-admin {
        background-color: #0f172a;
        color: #ffffff;
        border: none;
        border-radius: 12px;
        padding: 10px 20px;
        font-weight: 700;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    .btn-search-admin:hover {
        background-color: #1e293b;
        color: #ffffff;
    }

    .btn-add-admin {
        background-color: #e30613;
        color: #ffffff;
        border: none;
        border-radius: 12px;
        padding: 10px 24px;
        font-weight: 700;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        box-shadow: 0 4px 10px rgba(227, 6, 19, 0.15);
        text-decoration: none;
    }

    .btn-add-admin:hover {
        background-color: #c40510;
        color: #ffffff;
        box-shadow: 0 6px 15px rgba(227, 6, 19, 0.25);
    }

    .car-display-card-admin {
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
        border: 1px solid #e2e8f0;
    }

    .car-display-card-admin:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.06);
        border-color: #cbd5e1;
    }

    .car-display-card-admin .image-wrapper {
        width: 100%;
        height: 160px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.5rem;
        background: #ffffff;
        border-radius: 16px;
        overflow: hidden;
    }

    .car-display-card-admin .image-wrapper img {
        max-width: 95%;
        max-height: 90%;
        object-fit: contain;
        filter: drop-shadow(0 10px 8px rgba(0, 0, 0, 0.1));
        transition: transform 0.4s ease;
    }

    .car-display-card-admin:hover .image-wrapper img {
        transform: scale(1.05);
    }

    .car-name-title {
        font-weight: 800;
        font-size: 1.4rem;
        color: #0f172a;
    }

    .specs-grid-admin {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px 10px;
        margin-bottom: 1.2rem;
    }

    .spec-item-admin {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .spec-icon-box-admin {
        width: 32px;
        height: 32px;
        border: 1.5px solid #0056b3;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #0056b3;
        font-size: 0.95rem;
        flex-shrink: 0;
        background-color: #f0f7ff;
    }

    .spec-text-details-admin {
        display: flex;
        flex-direction: column;
    }

    .spec-text-details-admin .spec-label {
        font-size: 0.6rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #94a3b8;
        letter-spacing: 0.5px;
        margin-bottom: 1px;
    }

    .spec-text-details-admin .spec-value {
        font-size: 0.72rem;
        font-weight: 800;
        color: #1e293b;
        text-transform: uppercase;
    }

    .car-price-amount {
        font-size: 1.25rem;
        font-weight: 800;
        color: #e30613;
    }

    .admin-actions-bar {
        border-top: 1px solid #f1f5f9;
        padding-top: 1rem;
        margin-top: 1.2rem;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
</style>
@endsection

@section('content')
<!-- Filter & Add Button Card -->
<div class="filter-card-admin">
    <div class="row align-items-center g-3">
        <div class="col-lg-8">
            <form method="GET" action="{{ route('admin.cars.index') }}" class="row g-2">
                <div class="col-md-6">
                    <input type="text" 
                           class="form-control form-control-custom" 
                           name="search" 
                           placeholder="Cari nama mobil, brand, plat nomor..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select class="form-select form-select-custom" name="status">
                        <option value="">Semua Status</option>
                        <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Tersedia</option>
                        <option value="rented" {{ request('status') == 'rented' ? 'selected' : '' }}>Disewa</option>
                        <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn-search-admin w-100">
                        <i class="bi bi-search"></i> Cari
                    </button>
                </div>
            </form>
        </div>
        <div class="col-lg-4 text-lg-end">
            <a href="{{ route('admin.cars.create') }}" class="btn-add-admin">
                <i class="bi bi-plus-lg"></i> Tambah Unit Mobil
            </a>
        </div>
    </div>
</div>

<!-- Cars Grid -->
@if($cars->count() > 0)
    <div class="row g-4">
        @foreach($cars as $car)
            <div class="col-lg-4 col-md-6">
                <div class="card car-display-card-admin">
                    <div>
                        <!-- Header & Badges -->
                        <div class="position-relative">
                            <div class="image-wrapper">
                                <img src="{{ $car->image_url }}" alt="{{ $car->name }}">
                            </div>
                            
                            <!-- Status Badge -->
                            <div style="position: absolute; top: 0; right: 0;">
                                @if($car->status === 'available')
                                    <span class="badge bg-success" style="box-shadow: 0 4px 10px rgba(40,167,69,0.15);">Tersedia</span>
                                @elseif($car->status === 'rented')
                                    <span class="badge bg-primary" style="box-shadow: 0 4px 10px rgba(13,110,253,0.15);">Disewa</span>
                                @else
                                    <span class="badge bg-warning text-dark" style="box-shadow: 0 4px 10px rgba(255,193,7,0.15);">Maintenance</span>
                                @endif
                            </div>
                        </div>

                        <!-- Car Main info -->
                        <div class="mb-3">
                            <h5 class="car-name-title mb-1">{{ $car->name }}</h5>
                            <p class="text-muted small mb-3" style="font-weight: 500;">
                                {{ $car->brand }} • {{ $car->type }} • {{ $car->year }}
                            </p>
                            
                            <div class="specs-grid-admin">
                                <!-- TEMPAT DUDUK -->
                                <div class="spec-item-admin">
                                    <div class="spec-icon-box-admin">
                                        <i class="bi bi-people-fill"></i>
                                    </div>
                                    <div class="spec-text-details-admin">
                                        <span class="spec-label">Kursi</span>
                                        <span class="spec-value">{{ $car->seats }}</span>
                                    </div>
                                </div>
                                <!-- BAGASI -->
                                <div class="spec-item-admin">
                                    <div class="spec-icon-box-admin">
                                        <i class="bi bi-briefcase-fill"></i>
                                    </div>
                                    <div class="spec-text-details-admin">
                                        <span class="spec-label">Bagasi</span>
                                        <span class="spec-value">{{ $car->seats > 5 ? '2' : '1' }}</span>
                                    </div>
                                </div>
                                <!-- Plat Nomor -->
                                <div class="spec-item-admin">
                                    <div class="spec-icon-box-admin">
                                        <i class="bi bi-card-heading"></i>
                                    </div>
                                    <div class="spec-text-details-admin">
                                        <span class="spec-label">No Plat</span>
                                        <span class="spec-value" style="font-size: 0.68rem;">{{ $car->plate_number }}</span>
                                    </div>
                                </div>
                                <!-- Warna -->
                                <div class="spec-item-admin">
                                    <div class="spec-icon-box-admin">
                                        <i class="bi bi-palette-fill"></i>
                                    </div>
                                    <div class="spec-text-details-admin">
                                        <span class="spec-label">Warna</span>
                                        <span class="spec-value">{{ $car->color }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center bg-light p-2.5 rounded-3 px-3">
                                <span class="text-muted small fw-bold">Harga Sewa</span>
                                <span class="car-price-amount">Rp {{ number_format($car->price_per_day, 0, ',', '.') }}<small class="text-muted font-weight-normal" style="font-size: 0.72rem;">/hari</small></span>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="admin-actions-bar">
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.cars.show', $car->id) }}" class="btn btn-sm btn-outline-dark flex-fill fw-bold rounded-pill">
                                <i class="bi bi-eye"></i> Detail
                            </a>
                            <a href="{{ route('admin.cars.edit', $car->id) }}" class="btn btn-sm btn-outline-warning flex-fill fw-bold rounded-pill">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <form method="POST" 
                                  action="{{ route('admin.cars.destroy', $car->id) }}" 
                                  onsubmit="return confirm('Yakin ingin menghapus mobil ini?')"
                                  class="flex-fill">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger w-100 fw-bold rounded-pill">
                                    <i class="bi bi-trash"></i> Hapus
                                </button>
                            </form>
                        </div>
                        
                        <!-- Toggle Status available/maintenance -->
                        @if($car->status !== 'rented')
                            <form method="POST" action="{{ route('admin.cars.toggleStatus', $car->id) }}">
                                @csrf
                                <button type="submit" class="btn btn-sm w-100 rounded-pill {{ $car->status === 'available' ? 'btn-outline-secondary' : 'btn-outline-success' }} fw-bold">
                                    <i class="bi bi-arrow-repeat"></i> 
                                    {{ $car->status === 'available' ? 'Set Maintenance' : 'Set Tersedia' }}
                                </button>
                            </form>
                        @endif
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
    <div class="card" style="border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.01); border: 1px solid #e2e8f0;">
        <div class="card-body text-center py-5">
            <i class="bi bi-car-front text-muted" style="font-size: 5rem; opacity: 0.5;"></i>
            <h5 class="mt-4 fw-bold text-dark">Belum Ada Mobil Terdaftar</h5>
            <p class="text-muted mb-4 small">Mulai tambahkan mobil pertama Anda untuk ditampilkan di katalog sewa.</p>
            <a href="{{ route('admin.cars.create') }}" class="btn-add-admin">
                <i class="bi bi-plus-lg"></i> Tambah Unit Mobil
            </a>
        </div>
    </div>
@endif
@endsection
