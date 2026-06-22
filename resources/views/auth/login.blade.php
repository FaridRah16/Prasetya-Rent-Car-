@extends('layouts.app')

@section('title', 'Masuk')

@section('styles')
<style>
    .auth-wrapper {
        min-height: 80vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem 0;
    }
    
    .auth-card {
        max-width: 450px;
        width: 100%;
    }
    
    .auth-card .card {
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,.1);
    }
    
    .auth-card .card-header {
        background: var(--primary-color);
        color: white;
        border-radius: 15px 15px 0 0 !important;
        padding: 1.5rem;
    }
    
    .auth-card .btn-primary {
        padding: 0.75rem;
        font-weight: 600;
    }
    
    .toggle-password {
        cursor: pointer;
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
        z-index: 5;
    }
    
    .toggle-password:hover {
        color: #495057;
    }
</style>
@endsection

@section('scripts')
<script>
    function togglePassword(fieldId, icon) {
        const field = document.getElementById(fieldId);
        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            field.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    }
</script>
@endsection

@section('content')
<div class="auth-wrapper">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="auth-card">
                    <div class="card">
                        <div class="card-header text-center">
                            <h4 class="mb-0">
                                <i class="bi bi-box-arrow-in-right"></i> Masuk ke Akun
                            </h4>
                        </div>
                        <div class="card-body p-4">
                            <form method="POST" action="{{ route('login') }}">
                                @csrf
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">
                                        <i class="bi bi-envelope"></i> Email
                                    </label>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email') }}" 
                                           placeholder="Masukkan email Anda"
                                           required 
                                           autofocus>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label">
                                        <i class="bi bi-lock"></i> Password
                                    </label>
                                    <div class="position-relative">
                                        <input type="password" 
                                               class="form-control @error('password') is-invalid @enderror" 
                                               id="password" 
                                               name="password" 
                                               placeholder="Masukkan password Anda"
                                               required>
                                        <i class="bi bi-eye toggle-password" onclick="togglePassword('password', this)"></i>
                                    </div>
                                    @error('password')
                                        <div class="invalid-feedback" style="display:block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3 d-flex justify-content-between align-items-center">
                                    <div class="form-check mb-0">
                                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                        <label class="form-check-label" for="remember">
                                            Ingat Saya
                                        </label>
                                    </div>
                                    <a href="{{ route('password.request') }}" class="text-decoration-none small">Lupa password?</a>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-box-arrow-in-right"></i> Masuk
                                    </button>
                                </div>
                            </form>

                            <hr class="my-4">

                            <div class="text-center">
                                <p class="mb-0">
                                    Belum punya akun? 
                                    <a href="{{ route('register') }}" class="text-decoration-none">
                                        <strong>Daftar Sekarang</strong>
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Akun demo hanya ditampilkan di environment lokal, tidak pernah di produksi --}}
                    @if(app()->environment('local'))
                    <!-- Login Info -->
                    <div class="card mt-3">
                        <div class="card-body">
                            <small class="text-muted">
                                <strong>Akun Demo:</strong><br>
                                Admin: admin@prasetyarentcar.com / password<br>
                                Customer: siti@customer.com / password<br>
                                Driver: budi@driver.com / password
                            </small>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
