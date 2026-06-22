@extends('layouts.app')

@section('title', $car->name)

@section('styles')
<style>
    .car-detail-hero {
        background: linear-gradient(135deg, #111827 0%, #1e293b 100%);
        color: white;
        padding: 3rem 0;
        position: relative;
        overflow: hidden;
        margin-bottom: 2rem;
    }
    
    .car-detail-hero::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 15px;
        background: #ffffff;
        border-radius: 20px 20px 0 0;
    }
    
    .car-main-image-container {
        background: #ffffff;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.03);
        border: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 380px;
        overflow: hidden;
        position: relative;
    }
    
    .car-main-image-container img {
        max-width: 90%;
        max-height: 90%;
        object-fit: contain;
        filter: drop-shadow(0 15px 12px rgba(0,0,0,0.1));
        transition: all 0.3s ease;
    }
    
    .expand-icon-custom {
        position: absolute;
        bottom: 20px;
        right: 20px;
        color: #f2721e;
        font-size: 1.25rem;
        cursor: pointer;
        transition: transform 0.2s;
    }
    
    .expand-icon-custom:hover {
        transform: scale(1.2);
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

    .car-voxy-title {
        font-weight: 850;
        font-size: 2.5rem;
        color: #0f172a;
        margin-bottom: 0.5rem;
    }

    .spec-header-title {
        font-weight: 700;
        font-size: 1.1rem;
        color: #1e293b;
        margin-bottom: 1.2rem;
        margin-top: 1.5rem;
    }

    /* 6 specifications 2-column grid */
    .specs-grid-show {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px 15px;
        margin-bottom: 2rem;
    }

    .spec-item-show {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .spec-icon-box-show {
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

    .spec-text-details-show {
        display: flex;
        flex-direction: column;
    }

    .spec-text-details-show .spec-label {
        font-size: 0.55rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #94a3b8;
        letter-spacing: 0.5px;
        margin-bottom: 1px;
    }

    .spec-text-details-show .spec-value {
        font-size: 0.75rem;
        font-weight: 800;
        color: #1e293b;
        text-transform: uppercase;
    }

    .info-banner-verify {
        background-color: #f0f7ff;
        border-radius: 12px;
        padding: 15px 20px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 2rem;
        border: 1px solid #e0f2fe;
    }

    .info-banner-verify i {
        color: #0284c7;
        font-size: 1.3rem;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .info-banner-verify p {
        margin: 0;
        font-size: 0.85rem;
        color: #0369a1;
        font-weight: 600;
        line-height: 1.5;
    }

    .action-prompt-title {
        font-weight: 800;
        font-size: 1.05rem;
        color: #0f172a;
        margin-bottom: 5px;
    }

    .action-prompt-text {
        font-size: 0.9rem;
        color: #64748b;
        margin-bottom: 1.5rem;
        line-height: 1.5;
    }

    .btn-orange-pill {
        background-color: #f2721e !important;
        color: #ffffff !important;
        border-radius: 30px;
        padding: 12px 36px !important;
        font-weight: 800;
        font-size: 0.95rem;
        text-transform: uppercase;
        border: none;
        box-shadow: 0 4px 15px rgba(242, 114, 30, 0.3);
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }

    .btn-orange-pill:hover {
        background-color: #d95d12 !important;
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(242, 114, 30, 0.4);
    }

    .btn-book-premium {
        background-color: #e30613 !important;
        color: #ffffff !important;
        border-radius: 30px;
        padding: 12px 36px !important;
        font-weight: 800;
        font-size: 0.95rem;
        text-transform: uppercase;
        border: none;
        box-shadow: 0 4px 15px rgba(227, 6, 19, 0.3);
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }

    .btn-book-premium:hover {
        background-color: #c40510 !important;
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(227, 6, 19, 0.4);
    }

    /* Animation */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animated-content {
        animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    /* Image Lightbox */
    .lightbox-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.92);
        z-index: 9999;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
    }

    .lightbox-overlay.active {
        display: flex;
    }

    .lightbox-img-wrapper {
        position: relative;
        max-width: 85vw;
        max-height: 80vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .lightbox-img-wrapper img {
        max-width: 100%;
        max-height: 80vh;
        object-fit: contain;
        border-radius: 8px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
        transition: opacity 0.3s ease;
    }

    .lightbox-close {
        position: fixed;
        top: 20px;
        right: 25px;
        color: #ffffff;
        font-size: 2rem;
        cursor: pointer;
        z-index: 10001;
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.1);
        border: none;
        transition: all 0.2s ease;
    }

    .lightbox-close:hover {
        background: rgba(255, 255, 255, 0.25);
        transform: scale(1.1);
    }

    .lightbox-nav {
        position: fixed;
        top: 50%;
        transform: translateY(-50%);
        color: #ffffff;
        font-size: 1.8rem;
        cursor: pointer;
        z-index: 10001;
        width: 52px;
        height: 52px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.1);
        border: none;
        transition: all 0.2s ease;
    }

    .lightbox-nav:hover {
        background: rgba(255, 255, 255, 0.25);
        transform: translateY(-50%) scale(1.1);
    }

    .lightbox-prev {
        left: 20px;
    }

    .lightbox-next {
        right: 20px;
    }

    .lightbox-counter {
        color: rgba(255, 255, 255, 0.7);
        font-size: 0.9rem;
        margin-top: 16px;
        font-weight: 500;
    }

    .lightbox-thumbnails {
        display: flex;
        gap: 8px;
        margin-top: 12px;
        justify-content: center;
        flex-wrap: wrap;
        max-width: 80vw;
    }

    .lightbox-thumb {
        width: 60px;
        height: 45px;
        border-radius: 6px;
        overflow: hidden;
        cursor: pointer;
        border: 2px solid transparent;
        opacity: 0.5;
        transition: all 0.2s ease;
    }

    .lightbox-thumb.active {
        border-color: #ffffff;
        opacity: 1;
    }

    .lightbox-thumb:hover {
        opacity: 0.85;
    }

    .lightbox-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
</style>
@endsection

@section('content')
<!-- Hero Section -->
<div class="car-detail-hero">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb text-white-50">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white-50 text-decoration-none">Beranda</a></li>
                <li class="breadcrumb-item"><a href="{{ route('cars.index') }}" class="text-white-50 text-decoration-none">Katalog</a></li>
                <li class="breadcrumb-item active text-white fw-semibold" aria-current="page">{{ $car->name }}</li>
            </ol>
        </nav>
        <h1 class="display-6 fw-extrabold text-white">{{ $car->name }}</h1>
        <p class="lead mb-0 text-white-50">
            <i class="bi bi-tag-fill"></i> {{ $car->brand }} • {{ $car->type }}
        </p>
    </div>
</div>

<div class="container my-5 animated-content">
    <div class="row">
        <!-- Left Column: Car Image & Gallery Grid -->
        <div class="col-lg-6 mb-4">
            <!-- Main Car Image Container -->
            <div class="car-main-image-container">
                <img id="car-main-img" src="{{ $car->image_url }}" alt="{{ $car->name }}">
                <i class="bi bi-arrows-angle-expand expand-icon-custom" onclick="openLightbox(0)" title="Perbesar foto"></i>
            </div>

            <!-- Additional Gallery Thumbnails Row -->
            <div class="gallery-thumbnails-row">
                @php
                    $galleryUrls = $car->gallery_urls;
                @endphp
                @foreach($galleryUrls as $i => $url)
                    <div class="thumbnail-box {{ $i === 0 ? 'active' : '' }}" data-url="{{ $url }}" onclick="changeMainImage(this, this.dataset.url)" ondblclick="openLightbox({{ $i }})" title="Klik 2x untuk perbesar">
                        <img src="{{ $url }}" alt="Gallery {{ $i + 1 }}">
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Right Column: Specs & Contact Information -->
        <div class="col-lg-6 ps-lg-5">
            <h2 class="car-voxy-title">{{ $car->name }}</h2>
            
            <h3 class="fw-extrabold text-danger mb-3">
                Rp {{ number_format($car->price_per_day, 0, ',', '.') }} <span class="text-muted fs-6 font-weight-normal">/ Hari</span>
            </h3>

            @php $rentedUntil = optional($car->activeBooking)->end_date; @endphp
            <div class="mb-4">
                @if($car->status === 'rented')
                    <span class="badge bg-warning text-dark rounded-pill px-3 py-2">
                        <i class="bi bi-clock-history"></i>
                        {{ $rentedUntil ? 'Disewa s/d ' . $rentedUntil->format('d M Y') : 'Sedang Disewa' }}
                    </span>
                    <p class="text-muted small mt-2 mb-0">
                        <i class="bi bi-info-circle"></i> Unit sedang disewa. Anda tetap dapat memesan untuk tanggal lain yang tersedia.
                    </p>
                @elseif($car->status === 'maintenance')
                    <span class="badge bg-secondary rounded-pill px-3 py-2">
                        <i class="bi bi-tools"></i> Dalam Perawatan
                    </span>
                @else
                    <span class="badge bg-success rounded-pill px-3 py-2">
                        <i class="bi bi-check-circle-fill"></i> Tersedia
                    </span>
                @endif
            </div>

            <div class="spec-header-title">Spesifikasi :</div>

            <div class="specs-grid-show">
                <!-- TEMPAT DUDUK -->
                <div class="spec-item-show">
                    <div class="spec-icon-box-show">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div class="spec-text-details-show">
                        <span class="spec-label">Tempat Duduk</span>
                        <span class="spec-value">{{ $car->seats }}</span>
                    </div>
                </div>
                <!-- BAGASI -->
                <div class="spec-item-show">
                    <div class="spec-icon-box-show">
                        <i class="bi bi-briefcase-fill"></i>
                    </div>
                    <div class="spec-text-details-show">
                        <span class="spec-label">Bagasi</span>
                        <span class="spec-value">{{ $car->seats > 5 ? '2' : '1' }}</span>
                    </div>
                </div>
                <!-- TRANSMISI -->
                <div class="spec-item-show">
                    <div class="spec-icon-box-show">
                        <i class="bi bi-gear-wide-connected"></i>
                    </div>
                    <div class="spec-text-details-show">
                        <span class="spec-label">Transmisi</span>
                        <span class="spec-value">{{ strtoupper($car->transmission ?? (in_array(strtolower($car->type), ['suv', 'mpv']) ? 'AUTOMATIC' : 'MANUAL/AUTO')) }}</span>
                    </div>
                </div>
                <!-- BAHAN BAKAR -->
                <div class="spec-item-show">
                    <div class="spec-icon-box-show">
                        <i class="bi bi-fuel-pump-fill"></i>
                    </div>
                    <div class="spec-text-details-show">
                        <span class="spec-label">Bahan Bakar</span>
                        <span class="spec-value">{{ strtoupper($car->fuel ?? (str_contains(strtolower($car->name), 'innova') ? 'DIESEL' : 'BENSIN')) }}</span>
                    </div>
                </div>
                <!-- ASURANSI KENDARAAN -->
                <div class="spec-item-show">
                    <div class="spec-icon-box-show">
                        <i class="bi bi-shield-fill-check"></i>
                    </div>
                    <div class="spec-text-details-show">
                        <span class="spec-label">Asuransi</span>
                        <span class="spec-value">YES</span>
                    </div>
                </div>
                <!-- PENGEMUDI -->
                <div class="spec-item-show">
                    <div class="spec-icon-box-show">
                        <i class="bi bi-steering"></i>
                    </div>
                    <div class="spec-text-details-show">
                        <span class="spec-label">Pengemudi</span>
                        <span class="spec-value">YES</span>
                    </div>
                </div>
            </div>

            <!-- Health & Verification Banner -->
            <div class="info-banner-verify">
                <i class="bi bi-shield-fill-plus"></i>
                <p>Kendaraan dan pengemudi sudah di verifikasi serta mengikuti protokol kesehatan untuk kebersihan unit dan swab berkala.</p>
            </div>

            <!-- Action Prompt -->
            <div>
                <h5 class="action-prompt-title">Ingin Menggunakan Unit Ini?</h5>
                <p class="action-prompt-text">Hubungi kami sekarang juga untuk mendapatkan informasi lebih lanjut dan detail ketersediaan kendaraan ini.</p>
                
                <div class="d-flex gap-3 flex-wrap mt-3">
                    @if($car->status !== 'maintenance')
                        @auth
                            <a href="{{ route('customer.bookings.create', ['car_id' => $car->id]) }}" class="btn-book-premium">
                                <i class="bi bi-calendar-check-fill"></i> Sewa Sekarang
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn-book-premium">
                                <i class="bi bi-box-arrow-in-right"></i> Login untuk Sewa
                            </a>
                        @endauth
                    @else
                        <button class="btn btn-secondary rounded-pill px-4 py-3 fw-bold text-uppercase fs-7" disabled>
                            <i class="bi bi-exclamation-octagon-fill"></i> Sedang Perawatan
                        </button>
                    @endif
                    
                    <a href="https://wa.me/{{ config('business.whatsapp') }}?text=Halo%20Prasetya%20RentCar,%20saya%20tertarik%20menyewa%20mobil%20{{ urlencode($car->name) }}" target="_blank" rel="noopener noreferrer" class="btn-orange-pill">
                        Hubungi Kami <i class="bi bi-whatsapp"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Description Card -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card" style="border: none; border-radius: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.01); border: 1px solid #f1f5f9;">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0">
                    <h5 class="mb-0 text-dark fw-bold"><i class="bi bi-file-text-fill text-danger"></i> Deskripsi Tambahan</h5>
                </div>
                <div class="card-body px-4 pb-4 pt-3">
                    <p class="mb-0 text-muted" style="line-height: 1.7; font-size: 1rem;">
                        {{ $car->description ?? 'Mobil berkualitas dengan performa terbaik, kabin yang nyaman, dan efisiensi bahan bakar yang tinggi. Siap menemani perjalanan dinas, keluarga, maupun wisata Anda.' }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Lightbox Modal -->
<div class="lightbox-overlay" id="lightboxOverlay" onclick="closeLightboxOnBg(event)">
    <button class="lightbox-close" onclick="closeLightbox()" title="Tutup">
        <i class="bi bi-x-lg"></i>
    </button>
    <button class="lightbox-nav lightbox-prev" onclick="navigateLightbox(-1)" title="Sebelumnya">
        <i class="bi bi-chevron-left"></i>
    </button>
    <button class="lightbox-nav lightbox-next" onclick="navigateLightbox(1)" title="Selanjutnya">
        <i class="bi bi-chevron-right"></i>
    </button>
    <div class="lightbox-img-wrapper">
        <img id="lightboxImg" src="" alt="Car Image">
    </div>
    <div class="lightbox-counter" id="lightboxCounter"></div>
    <div class="lightbox-thumbnails" id="lightboxThumbnails"></div>
</div>

<script>
    // Gallery image URLs
    const galleryImages = @json($galleryUrls);
    let currentLightboxIndex = 0;

    function changeMainImage(element, imageUrl) {
        document.getElementById('car-main-img').src = imageUrl;
        const thumbnails = document.querySelectorAll('.thumbnail-box');
        thumbnails.forEach(thumb => thumb.classList.remove('active'));
        element.classList.add('active');
    }

    function openLightbox(index) {
        currentLightboxIndex = index;
        updateLightboxImage();
        buildLightboxThumbnails();
        document.getElementById('lightboxOverlay').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        document.getElementById('lightboxOverlay').classList.remove('active');
        document.body.style.overflow = '';
    }

    function closeLightboxOnBg(event) {
        if (event.target === document.getElementById('lightboxOverlay')) {
            closeLightbox();
        }
    }

    function navigateLightbox(direction) {
        currentLightboxIndex += direction;
        if (currentLightboxIndex < 0) currentLightboxIndex = galleryImages.length - 1;
        if (currentLightboxIndex >= galleryImages.length) currentLightboxIndex = 0;
        updateLightboxImage();
        updateLightboxThumbnails();
    }

    function updateLightboxImage() {
        document.getElementById('lightboxImg').src = galleryImages[currentLightboxIndex];
        document.getElementById('lightboxCounter').textContent = (currentLightboxIndex + 1) + ' / ' + galleryImages.length;
    }

    function buildLightboxThumbnails() {
        const container = document.getElementById('lightboxThumbnails');
        container.innerHTML = '';
        galleryImages.forEach(function(url, i) {
            const thumb = document.createElement('div');
            thumb.className = 'lightbox-thumb' + (i === currentLightboxIndex ? ' active' : '');
            thumb.innerHTML = '<img src="' + url + '" alt="Thumb ' + (i + 1) + '">';
            thumb.onclick = function() {
                currentLightboxIndex = i;
                updateLightboxImage();
                updateLightboxThumbnails();
            };
            container.appendChild(thumb);
        });
    }

    function updateLightboxThumbnails() {
        const thumbs = document.querySelectorAll('.lightbox-thumb');
        thumbs.forEach(function(thumb, i) {
            thumb.classList.toggle('active', i === currentLightboxIndex);
        });
    }

    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        const overlay = document.getElementById('lightboxOverlay');
        if (!overlay.classList.contains('active')) return;
        if (e.key === 'Escape') closeLightbox();
        if (e.key === 'ArrowLeft') navigateLightbox(-1);
        if (e.key === 'ArrowRight') navigateLightbox(1);
    });
</script>
@endsection
