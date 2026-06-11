@extends('layouts.admin')

@section('title', 'Kelola Mobil')
@section('page-title', 'Kelola Mobil')

@section('content')
<!-- Filter & Add Button -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <form method="GET" action="{{ route('admin.cars.index') }}" class="row g-3">
                    <div class="col-md-5">
                        <input type="text" 
                               class="form-control" 
                               name="search" 
                               placeholder="Cari mobil, brand, atau plat..."
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-4">
                        <select class="form-select" name="status">
                            <option value="">Semua Status</option>
                            <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Tersedia</option>
                            <option value="rented" {{ request('status') == 'rented' ? 'selected' : '' }}>Disewa</option>
                            <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search"></i> Cari
                        </button>
                    </div>
                </form>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('admin.cars.create') }}" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> Tambah Mobil
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Cars Grid -->
@if($cars->count() > 0)
    <div class="row g-4">
        @foreach($cars as $car)
            <div class="col-lg-4 col-md-6">
                <div class="card h-100">
                    <!-- Car Image -->
                    <div style="height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); position: relative;" class="d-flex align-items-center justify-content-center">
                        @if($car->image)
                            <img src="{{ asset('storage/' . $car->image) }}" 
                                 alt="{{ $car->name }}" 
                                 style="width: 100%; height: 100%; object-fit: cover;">
                        @else
                            <i class="bi bi-car-front-fill text-white" style="font-size: 5rem;"></i>
                        @endif
                        
                        <!-- Status Badge -->
                        <div style="position: absolute; top: 10px; right: 10px;">
                            @if($car->status === 'available')
                                <span class="badge bg-success">Tersedia</span>
                            @elseif($car->status === 'rented')
                                <span class="badge bg-primary">Disewa</span>
                            @else
                                <span class="badge bg-warning text-dark">Maintenance</span>
                            @endif
                        </div>
                    </div>

                    <div class="card-body">
                        <h5 class="card-title mb-2">{{ $car->name }}</h5>
                        <p class="text-muted mb-3">
                            <i class="bi bi-tag"></i> {{ $car->brand }} • {{ $car->type }}
                        </p>

                        <div class="row mb-3">
                            <div class="col-6">
                                <small class="text-muted">Plat Nomor</small>
                                <p class="mb-0 fw-bold">{{ $car->plate_number }}</p>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Kursi</small>
                                <p class="mb-0 fw-bold">{{ $car->seats }} Kursi</p>
                            </div>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted">Harga Sewa</small>
                            <h5 class="text-primary mb-0">Rp {{ number_format($car->price_per_day, 0, ',', '.') }}/hari</h5>
                        </div>

                        <!-- Actions -->
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.cars.edit', $car->id) }}" 
                               class="btn btn-sm btn-warning flex-fill">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <a href="{{ route('admin.cars.show', $car->id) }}" 
                               class="btn btn-sm btn-info flex-fill">
                                <i class="bi bi-eye"></i> Detail
                            </a>
                            <form method="POST" 
                                  action="{{ route('admin.cars.destroy', $car->id) }}" 
                                  onsubmit="return confirm('Yakin ingin menghapus mobil ini?')"
                                  class="flex-fill">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger w-100">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>

                        <!-- Toggle Status -->
                        @if($car->status !== 'rented')
                            <form method="POST" action="{{ route('admin.cars.toggleStatus', $car->id) }}" class="mt-2">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-secondary w-100">
                                    <i class="bi bi-arrow-repeat"></i> 
                                    @if($car->status === 'available')
                                        Set Maintenance
                                    @else
                                        Set Tersedia
                                    @endif
                                </button>
                            </form>
                        @endif
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
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-car-front text-muted" style="font-size: 4rem;"></i>
            <h5 class="mt-3">Belum Ada Mobil</h5>
            <p class="text-muted mb-4">Tambahkan mobil pertama untuk memulai</p>
            <a href="{{ route('admin.cars.create') }}" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> Tambah Mobil
            </a>
        </div>
    </div>
@endif
@endsection
