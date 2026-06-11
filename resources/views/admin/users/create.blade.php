@extends('layouts.admin')

@section('title', 'Tambah User')
@section('page-title', 'Tambah User Baru')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-person"></i> Informasi User</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   name="name" 
                                   value="{{ old('name') }}"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-select @error('role') is-invalid @enderror" 
                                    name="role" 
                                    id="role"
                                    required>
                                <option value="">Pilih Role</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="customer" {{ old('role') == 'customer' ? 'selected' : '' }}>Customer</option>
                                <option value="driver" {{ old('role') == 'driver' ? 'selected' : '' }}>Driver</option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   name="email" 
                                   value="{{ old('email') }}"
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
                                   value="{{ old('phone') }}"
                                   placeholder="08xxxxxxxxxx"
                                   required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   name="password" 
                                   required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                            <input type="password" 
                                   class="form-control" 
                                   name="password_confirmation" 
                                   required>
                        </div>

                        <!-- Driver License Field (shown only if role is driver) -->
                        <div class="col-12" id="licenseField" style="display: none;">
                            <label class="form-label">Nomor SIM <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('license_number') is-invalid @enderror" 
                                   name="license_number" 
                                   value="{{ old('license_number') }}"
                                   placeholder="Contoh: B123456789">
                            @error('license_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-circle"></i> Simpan User
                </button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Batal
                </a>
            </div>
        </form>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-info-circle"></i> Keterangan Role</h5>
            </div>
            <div class="card-body">
                <h6><span class="badge bg-danger">Admin</span></h6>
                <p class="small">Full akses ke seluruh sistem</p>

                <h6><span class="badge bg-primary">Customer</span></h6>
                <p class="small">Dapat melakukan booking mobil</p>

                <h6><span class="badge bg-info">Driver</span></h6>
                <p class="small">Dapat melihat dan mengelola tugas antar/jemput</p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('role').addEventListener('change', function() {
        const licenseField = document.getElementById('licenseField');
        if (this.value === 'driver') {
            licenseField.style.display = 'block';
        } else {
            licenseField.style.display = 'none';
        }
    });

    // Show license field if driver is selected (for validation errors)
    @if(old('role') === 'driver')
        document.getElementById('licenseField').style.display = 'block';
    @endif
</script>
@endsection
