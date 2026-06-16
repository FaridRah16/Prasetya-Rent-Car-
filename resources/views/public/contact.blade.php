@extends('layouts.app')

@section('title', 'Kontak - Prasetya Rent Car')

@section('styles')
<style>
    .contact-hero {
        background: linear-gradient(135deg, #111827 0%, #1e293b 100%);
        color: white;
        padding: 4rem 0;
        position: relative;
        overflow: hidden;
    }
    
    .contact-hero::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 15px;
        background: #ffffff;
        border-radius: 20px 20px 0 0;
    }

    .contact-info-title {
        font-size: 1.8rem;
        font-weight: 800;
        color: #0f172a;
        position: relative;
        display: inline-block;
        margin-bottom: 2rem;
    }
    
    .contact-info-title::after {
        content: '';
        position: absolute;
        bottom: -6px;
        left: 0;
        width: 40px;
        height: 4px;
        background: #e30613;
        border-radius: 2px;
    }

    .contact-info-card {
        border-radius: 16px;
        border: 1px solid #f1f5f9;
        box-shadow: 0 4px 15px rgba(0,0,0,0.01);
        transition: all 0.3s ease;
        background: #ffffff;
        margin-bottom: 1rem;
    }

    .contact-info-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.03);
        border-color: #cbd5e1;
    }

    .contact-icon-box {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        color: #ffffff;
    }

    .form-card-custom {
        border: none;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.02);
        border: 1px solid #f1f5f9;
        background: #ffffff;
        padding: 1.8rem;
    }

    .form-control-custom {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 18px;
        font-weight: 500;
        color: #4a5568;
        background-color: #f8fafc;
        transition: all 0.3s ease;
    }
    
    .form-control-custom:focus {
        border-color: #e30613;
        background-color: #ffffff;
        box-shadow: 0 0 0 3px rgba(227, 6, 19, 0.1);
        outline: none;
    }

    .btn-send-custom {
        background-color: #e30613;
        color: #ffffff;
        border: none;
        border-radius: 30px;
        padding: 12px 24px;
        font-weight: 850;
        font-size: 0.95rem;
        transition: all 0.2s ease;
        box-shadow: 0 4px 12px rgba(227, 6, 19, 0.2);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        width: 100%;
        text-transform: uppercase;
    }

    .btn-send-custom:hover {
        background-color: #c40510;
        color: #ffffff;
        box-shadow: 0 6px 18px rgba(227, 6, 19, 0.3);
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
<div class="contact-hero">
    <div class="container text-center py-4">
        <h1 class="fw-bold mb-2 text-white">Hubungi Kami</h1>
        <p class="text-white-50 mb-0">Tim customer support kami siap melayani dan menjawab semua pertanyaan Anda.</p>
    </div>
</div>

<!-- Contact Content -->
<div class="container my-5 animated-content">
    <div class="row py-3">
        <!-- Left Side: Contact Information Details -->
        <div class="col-lg-5 mb-5 mb-lg-0">
            <h2 class="contact-info-title">Informasi Kontak</h2>
            
            <!-- Address -->
            <div class="card contact-info-card border-0">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="contact-icon-box me-3" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);">
                        <i class="bi bi-geo-alt-fill" style="font-size: 1.3rem;"></i>
                    </div>
                    <div>
                        <h6 class="mb-1 fw-bold text-dark">Alamat Kantor</h6>
                        <p class="text-muted small mb-0">Jl. Raya Utama No. 123, Jakarta Selatan, Indonesia</p>
                    </div>
                </div>
            </div>

            <!-- Phone -->
            <div class="card contact-info-card border-0">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="contact-icon-box me-3" style="background: linear-gradient(135deg, #064e3b 0%, #10b981 100%);">
                        <i class="bi bi-telephone-fill" style="font-size: 1.3rem;"></i>
                    </div>
                    <div>
                        <h6 class="mb-1 fw-bold text-dark">Telepon Hotline</h6>
                        <p class="text-muted small mb-0">+62 812-3456-789</p>
                    </div>
                </div>
            </div>

            <!-- Email -->
            <div class="card contact-info-card border-0">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="contact-icon-box me-3" style="background: linear-gradient(135deg, #e30613 0%, #ab040e 100%);">
                        <i class="bi bi-envelope-fill" style="font-size: 1.3rem;"></i>
                    </div>
                    <div>
                        <h6 class="mb-1 fw-bold text-dark">Email Support</h6>
                        <p class="text-muted small mb-0">info@prasetyarentcar.com</p>
                    </div>
                </div>
            </div>

            <!-- WhatsApp -->
            <div class="card contact-info-card border-0">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="contact-icon-box me-3" style="background: linear-gradient(135deg, #25d366 0%, #128c7e 100%);">
                        <i class="bi bi-whatsapp" style="font-size: 1.3rem;"></i>
                    </div>
                    <div>
                        <h6 class="mb-1 fw-bold text-dark">WhatsApp Business</h6>
                        <p class="text-muted small mb-0">+62 812-3456-789</p>
                    </div>
                </div>
            </div>

            <!-- Operational Hours -->
            <div class="card contact-info-card border-0">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="contact-icon-box me-3" style="background: linear-gradient(135deg, #78350f 0%, #f59e0b 100%);">
                        <i class="bi bi-clock-fill" style="font-size: 1.3rem;"></i>
                    </div>
                    <div>
                        <h6 class="mb-1 fw-bold text-dark">Jam Operasional</h6>
                        <p class="text-muted small mb-0">Senin - Minggu: 08:00 - 22:00 WIB</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Send Message Form -->
        <div class="col-lg-7">
            <div class="card form-card-custom">
                <div class="card-body p-2">
                    <h4 class="fw-bold mb-4 text-dark" style="position: relative; display: inline-block;">
                        Kirim Pesan Masukan
                        <span style="position: absolute; bottom: -6px; left: 0; width: 30px; height: 3px; background: #e30613; border-radius: 2px;"></span>
                    </h4>
                    
                    <form>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="contact-name" class="form-label text-muted small fw-bold text-uppercase">Nama Lengkap</label>
                                <input type="text" class="form-control form-control-custom" id="contact-name" placeholder="Ketik nama Anda...">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="contact-email" class="form-label text-muted small fw-bold text-uppercase">Email</label>
                                <input type="email" class="form-control form-control-custom" id="contact-email" placeholder="Ketik email aktif...">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="contact-phone" class="form-label text-muted small fw-bold text-uppercase">No. Telepon / WA</label>
                            <input type="text" class="form-control form-control-custom" id="contact-phone" placeholder="Ketik nomor telepon aktif...">
                        </div>
                        <div class="mb-3">
                            <label for="contact-subject" class="form-label text-muted small fw-bold text-uppercase">Subjek</label>
                            <input type="text" class="form-control form-control-custom" id="contact-subject" placeholder="Ketik subjek pesan...">
                        </div>
                        <div class="mb-4">
                            <label for="contact-message" class="form-label text-muted small fw-bold text-uppercase">Isi Pesan</label>
                            <textarea class="form-control form-control-custom" id="contact-message" rows="5" placeholder="Tulis isi pesan Anda di sini..."></textarea>
                        </div>
                        <button type="button" class="btn-send-custom" onclick="alert('Terima kasih! Pesan Anda telah kami terima dan akan segera diproses oleh tim kami.')">
                            <i class="bi bi-send-fill"></i> Kirim Pesan Sekarang
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
