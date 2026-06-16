@extends('layouts.admin')

@section('title', 'Edit User')
@section('page-title', 'Edit User')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
            @csrf
            @method('PUT')

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
                                   value="{{ old('name', $user->name) }}"
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
                                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="customer" {{ old('role', $user->role) == 'customer' ? 'selected' : '' }}>Customer</option>
                                <option value="driver" {{ old('role', $user->role) == 'driver' ? 'selected' : '' }}>Driver</option>
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
                                   value="{{ old('email', $user->email) }}"
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Nomor Telepon <span class="text-danger">*</span></label>
                            <input type="tel" 
                                   class="form-control @error('phone') is-invalid @enderror" 
                                   name="phone" 
                                   value="{{ old('phone', $user->phone) }}"
                                   placeholder="08xxxxxxxxxx"
                                   pattern="[0-9]+"
                                   oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                                   required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label"><i class="bi bi-whatsapp text-success"></i> Nomor WhatsApp</label>
                            <input type="tel" 
                                   class="form-control @error('whatsapp_number') is-invalid @enderror" 
                                   name="whatsapp_number" 
                                   value="{{ old('whatsapp_number', $user->whatsapp_number) }}"
                                   placeholder="08xxxxxxxxxx (opsional)"
                                   pattern="[0-9]+"
                                   oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                            @error('whatsapp_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <hr>
                            <p class="text-muted mb-2">
                                <i class="bi bi-info-circle"></i> Kosongkan password jika tidak ingin mengubahnya
                            </p>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Password Baru</label>
                            <div class="position-relative">
                                <input type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       name="password"
                                       id="password">
                                <i class="bi bi-eye toggle-password" style="cursor:pointer;position:absolute;right:12px;top:50%;transform:translateY(-50%);color:#6c757d;z-index:5" onclick="togglePassword('password', this)"></i>
                            </div>
                            @error('password')
                                <div class="invalid-feedback" style="display:block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Minimal 8 karakter</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Konfirmasi Password Baru</label>
                            <div class="position-relative">
                                <input type="password" 
                                       class="form-control" 
                                       name="password_confirmation"
                                       id="password_confirmation">
                                <i class="bi bi-eye toggle-password" style="cursor:pointer;position:absolute;right:12px;top:50%;transform:translateY(-50%);color:#6c757d;z-index:5" onclick="togglePassword('password_confirmation', this)"></i>
                            </div>
                        </div>

                        <!-- Driver License Field (shown only if role is driver) -->
                        <div class="col-12" id="licenseField" style="display: {{ old('role', $user->role) === 'driver' ? 'block' : 'none' }};">
                            <hr>
                            <label class="form-label">Nomor SIM <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('license_number') is-invalid @enderror" 
                                   name="license_number" 
                                   value="{{ old('license_number', $user->driver->license_number ?? '') }}"
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
                    <i class="bi bi-check-circle"></i> Update User
                </button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Batal
                </a>
            </div>
        </form>
    </div>

    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-info-circle"></i> Info User</h5>
            </div>
            <div class="card-body">
                <p class="mb-2">
                    <small class="text-muted">Terdaftar pada</small><br>
                    <strong>{{ $user->created_at->format('d M Y H:i') }}</strong>
                </p>
                <p class="mb-0">
                    <small class="text-muted">Terakhir update</small><br>
                    <strong>{{ $user->updated_at->format('d M Y H:i') }}</strong>
                </p>
            </div>
        </div>

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

    // Auto-format WhatsApp number: 08xx → 628xx
    document.querySelectorAll('input[name="whatsapp_number"]').forEach(function(input) {
        input.addEventListener('blur', function() {
            let val = this.value.replace(/[^0-9]/g, '');
            if (val.startsWith('0')) {
                val = '62' + val.substring(1);
            }
            this.value = val;
        });
    });
</script>
@endsection
