@extends('layouts.app')

@section('title', 'Lupa Password')

@section('content')
<div style="min-height: 80vh; display: flex; align-items: center; padding: 2rem 0;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card" style="border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,.1);">
                    <div class="card-header text-center" style="background: var(--primary-color); color: #fff; border-radius: 15px 15px 0 0; padding: 1.5rem;">
                        <h4 class="mb-0"><i class="bi bi-key"></i> Lupa Password</h4>
                    </div>
                    <div class="card-body p-4">
                        @if(session('success'))
                            <div class="alert alert-success">
                                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                            </div>
                        @endif

                        <p class="text-muted">Masukkan email akun Anda. Jika terdaftar, kami akan mengirim link untuk mereset password.</p>

                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label"><i class="bi bi-envelope"></i> Email</label>
                                <input type="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email" value="{{ old('email') }}" required autofocus>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary"><i class="bi bi-send"></i> Kirim Link Reset</button>
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
