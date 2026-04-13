@extends('layouts.app')

@section('content')
<style>
    body { background-color: #f8fafc; }
    .auth-card { border-radius: 20px; border: none; overflow: hidden; }
    .auth-left { background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%); color: white; padding: 40px; display: flex; flex-direction: column; justify-content: center; }
    .custom-input { border-radius: 10px; padding: 12px 15px; border: 1px solid #e2e8f0; background-color: #f8fafc; }
    .custom-input:focus { border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); background-color: #fff; }
    .btn-brand { background-color: #4f46e5; color: white; border-radius: 10px; padding: 12px; font-weight: bold; transition: all 0.3s; }
    .btn-brand:hover { background-color: #4338ca; color: white; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(79, 70, 229, 0.3); }
</style>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card auth-card shadow-lg">
                <div class="row g-0">
                    <div class="col-md-5 auth-left d-none d-md-flex text-center">
                        <h2 class="fw-bolder mb-3">Selamat Datang Kembali!</h2>
                        <p class="opacity-75">Sistem Cerdas Alokasi Pengajar Codero menggunakan Algoritma K-Means Clustering.</p>
                    </div>

                    <div class="col-md-7 p-5">
                        <h4 class="fw-bold text-dark mb-1">Masuk ke Akun Anda</h4>
                        <p class="text-muted mb-4">Silakan masukkan kredensial Anda untuk melanjutkan.</p>

                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="email" class="form-label fw-semibold text-secondary small">Alamat Email</label>
                                <input id="email" type="email" class="form-control custom-input @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="contoh@codero.id">
                                @error('email')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="password" class="form-label fw-semibold text-secondary small mb-0">Kata Sandi</label>
                                    @if (Route::has('password.request'))
                                        {{-- <a class="text-decoration-none small text-primary fw-semibold" href="{{ route('password.request') }}">Lupa Sandi?</a> --}}
                                    @endif
                                </div>
                                <input id="password" type="password" class="form-control custom-input mt-2 @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="••••••••">
                                @error('password')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-brand w-100 border-0">Masuk Dashboard ➔</button>

                            {{-- <div class="text-center mt-4">
                                <span class="text-muted small">Belum punya akun? <a href="{{ route('register') }}" class="text-primary fw-bold text-decoration-none">Daftar sekarang</a></span>
                            </div> --}}
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
