@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <div class="display-5">⛺</div>
                    <h4 class="fw-bold mb-0">Masuk ke RentVentures</h4>
                    <small class="text-muted">Sewa alat camping jadi lebih mudah</small>
                </div>

                <form action="{{ route('login.process') }}" method="POST" novalidate>
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                               class="form-control @error('email') is-invalid @enderror"
                               placeholder="nama@email.com" autofocus>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" id="password" name="password"
                               class="form-control @error('password') is-invalid @enderror"
                               placeholder="Masukkan password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label" for="remember">Ingat saya</label>
                    </div>

                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-box-arrow-in-right"></i> Login
                    </button>
                </form>

                <p class="text-center mt-3 mb-0">
                    Belum punya akun? <a href="{{ route('register') }}">Daftar sebagai penyewa</a>
                </p>
            </div>
        </div>

        <div class="alert alert-light border small mt-3 mb-0">
            <strong>Akun uji coba</strong> (password: <code>password</code>)<br>
            Admin: <code>admin@rentventures.test</code><br>
            Penyewa: <code>penyewa@rentventures.test</code>
        </div>
    </div>
</div>
@endsection
