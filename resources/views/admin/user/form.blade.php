@extends('layouts.master')

@section('title', isset($user) ? 'Edit Pengguna' : 'Tambah Pengguna Baru')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-8">
        <h4 class="fw-bold mb-1">{{ isset($user) ? 'Edit Data Pengguna' : 'Tambah Pengguna Baru' }}</h4>
        <p class="text-muted">Lengkapi form di bawah ini untuk mengatur akun akses sistem.</p>
    </div>
    <div class="col-md-4 text-md-end">
        <a href="{{ route('admin.user.index') }}" class="btn btn-light border shadow-sm px-4">🔙 Kembali</a>
    </div>
</div>

@if ($errors->any())
<div class="alert alert-danger border-0 shadow-sm mb-4">
    <ul class="mb-0">@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
</div>
@endif

<div class="row">
    <div class="col-md-8">
        <div class="card saas-card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ isset($user) ? route('admin.user.update', $user->id) : route('admin.user.store') }}" method="POST">
                    @csrf
                    @if(isset($user)) @method('PUT') @endif

                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control form-control-lg" placeholder="Contoh: Budi Santoso" value="{{ old('name', $user->name ?? '') }}" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold text-dark">Alamat Email</label>
                            <input type="email" name="email" class="form-control form-control-lg" placeholder="contoh@codero.com" value="{{ old('email', $user->email ?? '') }}" required>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold text-dark">Hak Akses (Role)</label>
                            <select name="role" class="form-select form-select-lg" required>
                                <option value="teacher" {{ old('role', $user->role ?? '') == 'teacher' ? 'selected' : '' }}>👨‍🏫 Teacher / Guru</option>
                                <option value="admin" {{ old('role', $user->role ?? '') == 'admin' ? 'selected' : '' }}>👑 Administrator</option>
                            </select>
                        </div>
                    </div>

                    <hr class="my-4">
                    <h6 class="fw-bold text-primary mb-3">Keamanan Akun</h6>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark">Password Akses</label>
                        <input type="text" name="password" class="form-control" placeholder="{{ isset($user) ? 'Kosongkan jika tidak ingin mengubah password' : 'Buat password minimal 6 karakter' }}" {{ isset($user) ? '' : 'required' }}>
                        @if(isset($user))
                            <small class="text-muted mt-1 d-block"><i>* Hanya isi kotak ini jika Anda (Admin) ingin me-reset password milik pengguna ini.</i></small>
                        @endif
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary px-5 py-2 fw-bold shadow-sm" style="background-color: #4f46e5; border: none;">
                            💾 {{ isset($user) ? 'Simpan Perubahan' : 'Buat Akun Sekarang' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
