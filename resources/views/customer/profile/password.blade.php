@extends('layouts.customer')

@section('title', 'Ganti Password')
@section('page-title', 'Ganti Password')

@section('styles')
<style>
    .password-strength {
        height: 5px;
        border-radius: 3px;
        margin-top: 5px;
        transition: all 0.3s;
    }
    
    .strength-weak { background: #dc3545; width: 33%; }
    .strength-medium { background: #ffc107; width: 66%; }
    .strength-strong { background: #28a745; width: 100%; }
    
    .toggle-password {
        cursor: pointer;
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
    }
</style>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Change Password Form -->
        <div class="card mb-4">
            <div class="card-header bg-warning">
                <h5 class="mb-0"><i class="bi bi-key"></i> Ganti Password</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('customer.profile.updatePassword') }}">
                    @csrf
                    @method('PUT')

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        <strong>Tips Keamanan:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Gunakan kombinasi huruf besar, huruf kecil, angka, dan simbol</li>
                            <li>Minimal 8 karakter</li>
                            <li>Jangan gunakan password yang mudah ditebak</li>
                            <li>Jangan gunakan password yang sama dengan akun lain</li>
                        </ul>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Password Saat Ini <span class="text-danger">*</span></label>
                        <div class="position-relative">
                            <input type="password" 
                                   class="form-control @error('current_password') is-invalid @enderror" 
                                   name="current_password"
                                   id="currentPassword"
                                   required>
                            <i class="bi bi-eye toggle-password" onclick="togglePassword('currentPassword', this)"></i>
                        </div>
                        @error('current_password')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr>

                    <div class="mb-4">
                        <label class="form-label">Password Baru <span class="text-danger">*</span></label>
                        <div class="position-relative">
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   name="password"
                                   id="newPassword"
                                   required
                                   oninput="checkPasswordStrength()">
                            <i class="bi bi-eye toggle-password" onclick="togglePassword('newPassword', this)"></i>
                        </div>
                        <div class="password-strength" id="strengthBar"></div>
                        <small class="text-muted" id="strengthText">Minimal 8 karakter</small>
                        @error('password')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                        <div class="position-relative">
                            <input type="password" 
                                   class="form-control" 
                                   name="password_confirmation"
                                   id="confirmPassword"
                                   required>
                            <i class="bi bi-eye toggle-password" onclick="togglePassword('confirmPassword', this)"></i>
                        </div>
                        <small class="text-muted">Masukkan password baru sekali lagi</small>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-check-circle"></i> Ubah Password
                        </button>
                        <a href="{{ route('customer.profile.edit') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Security Tips -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-shield-check"></i> Tips Keamanan</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-check-circle text-success me-2" style="font-size: 1.5rem;"></i>
                            <div>
                                <strong>Ganti Password Secara Berkala</strong>
                                <p class="text-muted small mb-0">Ubah password Anda setiap 3-6 bulan sekali</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-check-circle text-success me-2" style="font-size: 1.5rem;"></i>
                            <div>
                                <strong>Jangan Bagikan Password</strong>
                                <p class="text-muted small mb-0">Jangan berikan password kepada siapapun</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-check-circle text-success me-2" style="font-size: 1.5rem;"></i>
                            <div>
                                <strong>Gunakan Password Unik</strong>
                                <p class="text-muted small mb-0">Jangan gunakan password yang sama di berbagai akun</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-check-circle text-success me-2" style="font-size: 1.5rem;"></i>
                            <div>
                                <strong>Logout Setelah Selesai</strong>
                                <p class="text-muted small mb-0">Selalu logout terutama di komputer umum</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Toggle password visibility
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

    // Check password strength
    function checkPasswordStrength() {
        const password = document.getElementById('newPassword').value;
        const strengthBar = document.getElementById('strengthBar');
        const strengthText = document.getElementById('strengthText');
        
        if (password.length === 0) {
            strengthBar.className = 'password-strength';
            strengthText.textContent = 'Minimal 8 karakter';
            strengthText.className = 'text-muted';
            return;
        }
        
        let strength = 0;
        
        // Check length
        if (password.length >= 8) strength++;
        if (password.length >= 12) strength++;
        
        // Check for lowercase
        if (password.match(/[a-z]/)) strength++;
        
        // Check for uppercase
        if (password.match(/[A-Z]/)) strength++;
        
        // Check for numbers
        if (password.match(/[0-9]/)) strength++;
        
        // Check for special characters
        if (password.match(/[^a-zA-Z0-9]/)) strength++;
        
        // Display strength
        if (strength < 3) {
            strengthBar.className = 'password-strength strength-weak';
            strengthText.textContent = 'Password lemah';
            strengthText.className = 'text-danger small';
        } else if (strength < 5) {
            strengthBar.className = 'password-strength strength-medium';
            strengthText.textContent = 'Password sedang';
            strengthText.className = 'text-warning small';
        } else {
            strengthBar.className = 'password-strength strength-strong';
            strengthText.textContent = 'Password kuat';
            strengthText.className = 'text-success small';
        }
    }
</script>
@endsection
