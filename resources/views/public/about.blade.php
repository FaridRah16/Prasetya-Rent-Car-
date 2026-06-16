@extends('layouts.app')

@section('title', 'Tentang Kami - Prasetya Rent Car')

@section('styles')
<style>
    .about-hero {
        background: linear-gradient(135deg, #111827 0%, #1e293b 100%);
        color: white;
        padding: 4rem 0;
        position: relative;
        overflow: hidden;
    }
    
    .about-hero::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 15px;
        background: #ffffff;
        border-radius: 20px 20px 0 0;
    }

    .about-image-card {
        height: 350px;
        background: linear-gradient(135deg, #e30613 0%, #ab040e 100%);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 10px 30px rgba(227, 6, 19, 0.15);
        border: none;
    }

    .about-section-title {
        font-size: 2.3rem;
        font-weight: 800;
        color: #0f172a;
        position: relative;
        display: inline-block;
        margin-bottom: 1.5rem;
    }
    
    .about-section-title::after {
        content: '';
        position: absolute;
        bottom: -8px;
        left: 0;
        width: 50px;
        height: 4px;
        background: #e30613;
        border-radius: 2px;
    }

    .advantage-card {
        border-radius: 20px;
        border: 1px solid #f1f5f9;
        box-shadow: 0 10px 25px rgba(0,0,0,0.01);
        transition: all 0.3s ease;
        height: 100%;
        background: #ffffff;
    }

    .advantage-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.04);
        border-color: #cbd5e1;
    }

    .advantage-icon-wrapper {
        width: 80px;
        height: 80px;
        border-radius: 16px;
        margin: 0 auto 1.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
    }

    /* Animations */
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
        animation: fadeInUp 0.7s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
</style>
@endsection

@section('content')
<!-- Hero Section -->
<div class="about-hero">
    <div class="container text-center py-4">
        <h1 class="fw-bold mb-2 text-white">Tentang Kami</h1>
        <p class="text-white-50 mb-0">Kenali lebih dekat Prasetya Rent Car, penyedia sewa mobil terpercaya Anda.</p>
    </div>
</div>

<!-- About Content -->
<div class="container my-5 animated-content">
    <div class="row align-items-center mb-5 py-4">
        <div class="col-lg-6 mb-4 mb-lg-0">
            <div class="about-image-card text-center text-white">
                <div>
                    <i class="bi bi-car-front-fill" style="font-size: 6rem; filter: drop-shadow(0 10px 10px rgba(0,0,0,0.15));"></i>
                    <h3 class="mt-3 fw-bold">Prasetya Rent Car</h3>
                    <p class="text-white-50 small mb-0">Professional Car Rental Services</p>
                </div>
            </div>
        </div>
        <div class="col-lg-6 ps-lg-5">
            <h2 class="about-section-title">Siapa Kami?</h2>
            <p class="text-muted" style="line-height: 1.7;">
                Prasetya Rent Car adalah perusahaan penyedia layanan rental mobil terdepan yang berkomitmen 
                memberikan pengalaman berkendara paling aman dan menyenangkan bagi setiap pelanggan. Dengan armada terawat 
                serta jajaran pengemudi profesional, kami siap mendukung segala keperluan mobilitas harian maupun bisnis Anda.
            </p>
            <p class="text-muted" style="line-height: 1.7;">
                Kami menghadirkan portofolio unit kendaraan lengkap, mulai dari city car yang lincah hingga MPV premium keluarga, 
                semuanya terawat secara berkala guna menjamin keselamatan perjalanan Anda.
            </p>
            
            <div class="row mt-4">
                <div class="col-6">
                    <div class="d-flex align-items-center mb-3 text-dark fw-semibold gap-2">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 1.25rem;"></i>
                        <span>Armada Selalu Terawat</span>
                    </div>
                    <div class="d-flex align-items-center mb-3 text-dark fw-semibold gap-2">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 1.25rem;"></i>
                        <span>Driver Ramah & Profesional</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="d-flex align-items-center mb-3 text-dark fw-semibold gap-2">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 1.25rem;"></i>
                        <span>Harga Jujur & Terjangkau</span>
                    </div>
                    <div class="d-flex align-items-center mb-3 text-dark fw-semibold gap-2">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 1.25rem;"></i>
                        <span>Bantuan CS Siaga 24/7</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Keunggulan Section -->
    <div class="row mt-5 pt-4">
        <div class="col-12 text-center mb-5">
            <h2 class="fw-bold text-dark mb-2">Mengapa Memilih Kami?</h2>
            <p class="text-muted">Keunggulan istimewa layanan sewa mobil di tempat kami</p>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card advantage-card text-center border-0 p-4">
                <div class="card-body">
                    <div class="advantage-icon-wrapper" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);">
                        <i class="bi bi-shield-check" style="font-size: 2.2rem;"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Aman & Terproteksi</h5>
                    <p class="text-muted small mb-0" style="line-height: 1.6;">Semua kendaraan kami dilindungi oleh asuransi komprehensif demi meminimalisasi kekhawatiran Anda di jalan.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card advantage-card text-center border-0 p-4">
                <div class="card-body">
                    <div class="advantage-icon-wrapper" style="background: linear-gradient(135deg, #e30613 0%, #ab040e 100%);">
                        <i class="bi bi-headset" style="font-size: 2.2rem;"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Customer Service 24/7</h5>
                    <p class="text-muted small mb-0" style="line-height: 1.6;">Tim support kami senantiasa bersiap siaga melayani pemesanan atau menangani kendala darurat Anda kapan pun.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card advantage-card text-center border-0 p-4">
                <div class="card-body">
                    <div class="advantage-icon-wrapper" style="background: linear-gradient(135deg, #064e3b 0%, #10b981 100%);">
                        <i class="bi bi-cash-coin" style="font-size: 2.2rem;"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Transparansi Harga</h5>
                    <p class="text-muted small mb-0" style="line-height: 1.6;">Tidak ada biaya siluman atau tambahan misterius. Semua transaksi tercatat jujur dan transparan.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
