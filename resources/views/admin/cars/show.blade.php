@extends('layouts.admin')

@section('title', 'Detail Mobil')
@section('page-title', 'Detail Mobil: ' . $car->name)

@section('styles')
<style>
    .car-main-image-container {
        background: #ffffff;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.01);
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 350px;
        overflow: hidden;
        position: relative;
    }
    
    .car-main-image-container img {
        max-width: 90%;
        max-height: 90%;
        object-fit: contain;
        filter: drop-shadow(0 15px 12px rgba(0,0,0,0.1));
    }
    
    .gallery-thumbnails-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 15px;
        margin-top: 20px;
    }

    .thumbnail-box {
        border-radius: 12px;
        border: 1.5px solid #e2e8f0;
        padding: 8px;
        background: #ffffff;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 80px;
        overflow: hidden;
        transition: all 0.2s ease;
    }

    .thumbnail-box:hover {
        border-color: #cbd5e1;
        transform: translateY(-2px);
    }

    .thumbnail-box.active {
        border-color: #0056b3;
        box-shadow: 0 0 0 3px rgba(0, 86, 179, 0.1);
    }

    .thumbnail-box img {
        max-width: 95%;
        max-height: 90%;
        object-fit: contain;
    }

    .specs-grid-admin-show {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px 15px;
    }

    .spec-item-admin-show {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .spec-icon-box-admin-show {
        width: 38px;
        height: 38px;
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

    .spec-text-details-admin-show {
        display: flex;
        flex-direction: column;
    }

    .spec-text-details-admin-show .spec-label {
        font-size: 0.58rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #94a3b8;
        letter-spacing: 0.5px;
        margin-bottom: 1px;
    }

    .spec-text-details-admin-show .spec-value {
        font-size: 0.76rem;
        font-weight: 800;
        color: #1e293b;
        text-transform: uppercase;
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <!-- Main Car Image & Gallery -->
        <div class="card mb-4" style="border: none; background: transparent; box-shadow: none;">
            <div class="car-main-image-container">
                <img id="car-main-img" src="{{ $car->image_url }}" alt="{{ $car->name }}">
            </div>

            <!-- Additional Gallery Thumbnails Row -->
            <div class="gallery-thumbnails-row">
                @php
                    $galleryUrls = $car->gallery_urls;
                @endphp
                @foreach($galleryUrls as $i => $url)
                    <div class="thumbnail-box {{ $i === 0 ? 'active' : '' }}" onclick="changeMainImage(this, '{{ $url }}')">
                        <img src="{{ $url }}" alt="Gallery {{ $i + 1 }}">
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Car Information Card -->
        <div class="card mb-4">
            <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0">
                <h5 class="mb-0 text-dark fw-bold"><i class="bi bi-info-circle-fill text-danger"></i> Informasi Spesifikasi Unit</h5>
            </div>
            <div class="card-body p-4">
                <div class="specs-grid-admin-show mb-4">
                    <!-- TEMPAT DUDUK -->
                    <div class="spec-item-admin-show">
                        <div class="spec-icon-box-admin-show">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <div class="spec-text-details-admin-show">
                            <span class="spec-label">Kursi</span>
                            <span class="spec-value">{{ $car->seats }}</span>
                        </div>
                    </div>
                    <!-- BAGASI -->
                    <div class="spec-item-admin-show">
                        <div class="spec-icon-box-admin-show">
                            <i class="bi bi-briefcase-fill"></i>
                        </div>
                        <div class="spec-text-details-admin-show">
                            <span class="spec-label">Bagasi</span>
                            <span class="spec-value">{{ $car->seats > 5 ? '2' : '1' }}</span>
                        </div>
                    </div>
                    <!-- TRANSMISI -->
                    <div class="spec-item-admin-show">
                        <div class="spec-icon-box-admin-show">
                            <i class="bi bi-gear-wide-connected"></i>
                        </div>
                        <div class="spec-text-details-admin-show">
                            <span class="spec-label">Transmisi</span>
                            <span class="spec-value">{{ in_array(strtolower($car->type), ['suv', 'mpv']) ? 'AUTOMATIC' : 'MANUAL/AUTO' }}</span>
                        </div>
                    </div>
                    <!-- BAHAN BAKAR -->
                    <div class="spec-item-admin-show">
                        <div class="spec-icon-box-admin-show">
                            <i class="bi bi-fuel-pump-fill"></i>
                        </div>
                        <div class="spec-text-details-admin-show">
                            <span class="spec-label">Bahan Bakar</span>
                            <span class="spec-value">{{ str_contains(strtolower($car->name), 'innova') ? 'DIESEL' : 'BENSIN' }}</span>
                        </div>
                    </div>
                    <!-- ASURANSI KENDARAAN -->
                    <div class="spec-item-admin-show">
                        <div class="spec-icon-box-admin-show">
                            <i class="bi bi-shield-fill-check"></i>
                        </div>
                        <div class="spec-text-details-admin-show">
                            <span class="spec-label">Asuransi</span>
                            <span class="spec-value">YES</span>
                        </div>
                    </div>
                    <!-- PENGEMUDI -->
                    <div class="spec-item-admin-show">
                        <div class="spec-icon-box-admin-show">
                            <i class="bi bi-steering"></i>
                        </div>
                        <div class="spec-text-details-admin-show">
                            <span class="spec-label">Pengemudi</span>
                            <span class="spec-value">YES</span>
                        </div>
                    </div>
                </div>

                <div class="row pt-3 border-top g-3">
                    <div class="col-md-4">
                        <small class="text-muted">Nama Mobil</small>
                        <p class="mb-0 fw-bold fs-6">{{ $car->name }}</p>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted">Nomor Plat</small>
                        <p class="mb-0 fw-bold fs-6 text-uppercase">{{ $car->plate_number }}</p>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted">Status</small>
                        <p class="mb-0">
                            @if($car->status === 'available')
                                <span class="badge bg-success">Tersedia</span>
                            @elseif($car->status === 'rented')
                                <span class="badge bg-primary">Disewa</span>
                            @else
                                <span class="badge bg-warning text-dark">Maintenance</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Description Card -->
        @if($car->description)
            <div class="card mb-4">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0">
                    <h5 class="mb-0 text-dark fw-bold"><i class="bi bi-file-text-fill text-danger"></i> Deskripsi Mobil</h5>
                </div>
                <div class="card-body px-4 pb-4">
                    <p class="mb-0 text-muted" style="line-height: 1.6;">{{ $car->description }}</p>
                </div>
            </div>
        @endif

        <!-- Booking History Card -->
        <div class="card mb-4">
            <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0">
                <h5 class="mb-0 text-dark fw-bold"><i class="bi bi-clock-history text-danger"></i> Riwayat Booking Terakhir</h5>
            </div>
            <div class="card-body px-4 pb-4 pt-3">
                @php
                    $bookings = $car->bookings()->with('user')->orderBy('created_at', 'desc')->take(5)->get();
                @endphp

                @if($bookings->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Customer</th>
                                    <th>Tanggal Sewa</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bookings as $booking)
                                    <tr>
                                        <td class="fw-bold">#{{ $booking->id }}</td>
                                        <td>{{ $booking->user->name }}</td>
                                        <td>{{ $booking->start_date->format('d M Y') }} s/d {{ $booking->end_date->format('d M Y') }}</td>
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
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center py-3 mb-0"><i class="bi bi-calendar-x fs-5 d-block mb-1"></i> Belum ada riwayat sewa</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Right Column Sidebar Actions -->
    <div class="col-lg-4">
        <!-- Actions Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Aksi Pengelolaan</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.cars.edit', $car->id) }}" class="btn btn-warning rounded-pill fw-bold text-white">
                        <i class="bi bi-pencil-square"></i> Edit Unit Mobil
                    </a>
                    
                    @if($car->status !== 'rented')
                        <form method="POST" action="{{ route('admin.cars.toggleStatus', $car->id) }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary w-100 rounded-pill fw-bold">
                                <i class="bi bi-arrow-repeat"></i> 
                                @if($car->status === 'available')
                                    Set Maintenance
                                @else
                                    Set Tersedia
                                @endif
                            </button>
                        </form>
                    @endif

                    <hr class="my-2">

                    <form method="POST" 
                          action="{{ route('admin.cars.destroy', $car->id) }}" 
                          onsubmit="return confirm('Yakin ingin menghapus mobil ini? Data tidak dapat dikembalikan!')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100 rounded-pill fw-bold">
                            <i class="bi bi-trash-fill"></i> Hapus Unit Mobil
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Statistics Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Metrik Unit</h5>
            </div>
            <div class="card-body">
                @php
                    $totalBookings = $car->bookings()->count();
                    $completedBookings = $car->bookings()->where('status', 'completed')->count();
                    $totalRevenue = $car->bookings()->where('payment_status', 'paid')->sum('total_price');
                @endphp

                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted small fw-bold">Total Booking</span>
                    <strong>{{ $totalBookings }} kali</strong>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted small fw-bold">Booking Selesai</span>
                    <strong>{{ $completedBookings }} kali</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted small fw-bold">Total Pendapatan</span>
                    <strong class="text-danger">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</strong>
                </div>
            </div>
        </div>

        <!-- Info Card -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Informasi Database</h5>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <small class="text-muted small fw-bold">ID Unit</small>
                    <p class="mb-0 fw-bold">#{{ $car->id }}</p>
                </div>
                <div class="mb-2">
                    <small class="text-muted small fw-bold">Tanggal Masuk</small>
                    <p class="mb-0">{{ $car->created_at->format('d M Y H:i') }} WIB</p>
                </div>
                <div>
                    <small class="text-muted small fw-bold">Update Terakhir</small>
                    <p class="mb-0">{{ $car->updated_at->format('d M Y H:i') }} WIB</p>
                </div>
            </div>
        </div>

        <a href="{{ route('admin.cars.index') }}" class="btn btn-outline-dark w-100 rounded-pill fw-bold mt-3">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar
        </a>
    </div>
</div>

<script>
    function changeMainImage(element, imageUrl) {
        document.getElementById('car-main-img').src = imageUrl;
        
        const thumbnails = document.querySelectorAll('.thumbnail-box');
        thumbnails.forEach(thumb => thumb.classList.remove('active'));
        
        element.classList.add('active');
    }
</script>
@endsection
