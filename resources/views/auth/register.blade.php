@extends('layouts.app')

@section('title', 'Daftar')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7 col-lg-6">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <div class="display-5">🏕️</div>
                    <h4 class="fw-bold mb-0">Daftar Akun Penyewa</h4>
                    <small class="text-muted">Isi data berikut untuk mulai menyewa alat</small>
                </div>

                <form action="{{ route('register.process') }}" method="POST" novalidate>
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Lengkap</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}"
                               class="form-control @error('name') is-invalid @enderror" autofocus>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                               class="form-control @error('email') is-invalid @enderror">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">No. HP <span class="text-muted">(opsional)</span></label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
                               class="form-control @error('phone') is-invalid @enderror" placeholder="08xxxxxxxxxx">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" id="password" name="password"
                                   class="form-control @error('password') is-invalid @enderror">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                   class="form-control">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-person-plus"></i> Daftar
                    </button>
                </form>

                <p class="text-center mt-3 mb-0">
                    Sudah punya akun? <a href="{{ route('login') }}">Login di sini</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
