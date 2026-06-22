@extends('layouts.app')

@section('title', 'Reset Password')

@section('content')
<div style="min-height: 80vh; display: flex; align-items: center; padding: 2rem 0;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card" style="border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,.1);">
                    <div class="card-header text-center" style="background: var(--primary-color); color: #fff; border-radius: 15px 15px 0 0; padding: 1.5rem;">
                        <h4 class="mb-0"><i class="bi bi-shield-lock"></i> Reset Password</h4>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('password.update') }}">
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">

                            <div class="mb-3">
                                <label for="email" class="form-label"><i class="bi bi-envelope"></i> Email</label>
                                <input type="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email" value="{{ old('email', $email) }}" required readonly>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label"><i class="bi bi-lock"></i> Password Baru</label>
                                <input type="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       id="password" name="password" required autofocus>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Minimal 8 karakter, mengandung huruf, angka, dan simbol.</small>
                            </div>

                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label"><i class="bi bi-lock-fill"></i> Konfirmasi Password Baru</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Reset Password</button>
                            </div>
                        </form>

                        <hr class="my-4">
                        <div class="text-center">
                            <a href="{{ route('login') }}" class="text-decoration-none"><i class="bi bi-arrow-left"></i> Kembali ke Login</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
