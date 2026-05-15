@extends('layouts.app')

@section('content')
<style>
    body { background-color: #f8fafc; }
    .auth-card { border-radius: 20px; border: none; overflow: hidden; }
    .auth-left { background: linear-gradient(135deg, #4f46e5 0%, #059669 100%); color: white; padding: 40px; display: flex; flex-direction: column; justify-content: center; }
    .custom-input { border-radius: 10px; padding: 12px 15px; border: 1px solid #e2e8f0; background-color: #f8fafc; }
    .custom-input:focus { border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1); background-color: #fff; }
    .btn-brand { background-color: #4f46e5; color: white; border-radius: 10px; padding: 12px; font-weight: bold; transition: all 0.3s; }
    .btn-brand:hover { background-color: #4338ca; color: white; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(16, 185, 129, 0.3); }
</style>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card auth-card shadow-lg">
                <div class="row g-0">

                    <div class="col-md-7 p-5">
                        <h4 class="fw-bold text-dark mb-1">Buat Akun Baru</h4>
                        <p class="text-muted mb-4">Daftarkan diri Anda sebagai Admin atau Pengajar.</p>

                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="name" class="form-label fw-semibold text-secondary small">Nama Lengkap</label>
                                <input id="name" type="text" class="form-control custom-input @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus placeholder="Masukkan nama Anda">
                                @error('name')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label fw-semibold text-secondary small">Alamat Email</label>
                                <input id="email" type="email" class="form-control custom-input @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="contoh@codero.id">
                                @error('email')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="password" class="form-label fw-semibold text-secondary small">Kata Sandi</label>
                                    <input id="password" type="password" class="form-control custom-input @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="••••••••">
                                    @error('password')
                                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                                <div class="col-md-6 mt-3 mt-md-0">
                                    <label for="password-confirm" class="form-label fw-semibold text-secondary small">Konfirmasi Sandi</label>
                                    <input id="password-confirm" type="password" class="form-control custom-input" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-brand w-100 border-0">Daftar Sekarang ➔</button>

                            <div class="text-center mt-4">
                                <span class="text-muted small">Sudah punya akun? <a href="{{ route('login') }}" class="text-primary fw-bold text-decoration-none">Masuk di sini</a></span>
                            </div>
                        </form>
                    </div>

                    <div class="col-md-5 auth-left d-none d-md-flex text-center">
                        <h2 class="fw-bolder mb-3">Mari Bergabung!</h2>
                        <p class="opacity-75">Jadilah bagian dari inovasi Codero dalam menentukan alokasi pengajar terbaik berbasis data.</p>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
