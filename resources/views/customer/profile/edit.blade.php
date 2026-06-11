@extends('layouts.customer')

@section('title', 'Edit Profile')
@section('page-title', 'Edit Profile')

@section('styles')
<style>
    .avatar-preview {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #1a3c5e;
    }
    
    .avatar-placeholder {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea, #764ba2);
        display: flex;
        align-items: center;
        justify-content: center;
        border: 4px solid #1a3c5e;
    }
    
    .upload-area {
        border: 2px dashed #dee2e6;
        border-radius: 10px;
        padding: 2rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .upload-area:hover {
        border-color: #1a3c5e;
        background: #f8f9fa;
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-4">
        <!-- Avatar Card -->
        <div class="card mb-4">
            <div class="card-body text-center">
                <h5 class="mb-3">Foto Profil</h5>
                
                @if(Auth::user()->avatar)
                    <img src="{{ asset('storage/' . Auth::user()->avatar) }}" 
                         alt="Avatar" 
                         class="avatar-preview mb-3"
                         id="avatarPreview">
                @else
                    <div class="avatar-placeholder mx-auto mb-3" id="avatarPlaceholder">
                        <i class="bi bi-person-fill text-white" style="font-size: 4rem;"></i>
                    </div>
                @endif

                <form method="POST" action="{{ route('customer.profile.update') }}" enctype="multipart/form-data" id="avatarForm">
                    @csrf
                    @method('PUT')
                    
                    <input type="hidden" name="name" value="{{ Auth::user()->name }}">
                    <input type="hidden" name="email" value="{{ Auth::user()->email }}">
                    <input type="hidden" name="phone" value="{{ Auth::user()->phone }}">
                    
                    <div class="mb-3">
                        <label for="avatarInput" class="btn btn-primary btn-sm">
                            <i class="bi bi-upload"></i> Upload Foto
                        </label>
                        <input type="file" 
                               id="avatarInput" 
                               name="avatar" 
                               accept="image/*" 
                               style="display: none;">
                    </div>
                </form>

                @if(Auth::user()->avatar)
                    <form method="POST" action="{{ route('customer.profile.deleteAvatar') }}" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin hapus foto profil?')">
                            <i class="bi bi-trash"></i> Hapus Foto
                        </button>
                    </form>
                @endif

                <p class="text-muted small mt-3 mb-0">
                    <i class="bi bi-info-circle"></i> Format: JPG, PNG (Max: 2MB)
                </p>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-gear"></i> Pengaturan</h5>
            </div>
            <div class="card-body d-grid gap-2">
                <a href="{{ route('customer.profile.password') }}" class="btn btn-outline-warning">
                    <i class="bi bi-key"></i> Ganti Password
                </a>
                <a href="{{ route('customer.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Profile Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-person"></i> Informasi Profil</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('customer.profile.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   name="name" 
                                   value="{{ old('name', Auth::user()->name) }}"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   name="email" 
                                   value="{{ old('email', Auth::user()->email) }}"
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Nomor Telepon <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('phone') is-invalid @enderror" 
                                   name="phone" 
                                   value="{{ old('phone', Auth::user()->phone) }}"
                                   placeholder="08xxxxxxxxxx"
                                   required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle"></i> Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Account Info -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-info-circle"></i> Informasi Akun</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <small class="text-muted">Role</small>
                        <p class="mb-0">
                            <span class="badge bg-primary">Customer</span>
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted">Status Akun</small>
                        <p class="mb-0">
                            <span class="badge bg-success">Aktif</span>
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted">Terdaftar Sejak</small>
                        <p class="mb-0">{{ Auth::user()->created_at->format('d F Y') }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted">Terakhir Update</small>
                        <p class="mb-0">{{ Auth::user()->updated_at->format('d F Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Preview avatar before upload
    document.getElementById('avatarInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Auto submit form when file selected
            document.getElementById('avatarForm').submit();
        }
    });
</script>
@endsection
