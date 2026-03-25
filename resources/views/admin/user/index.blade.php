@extends('layouts.master')

@section('title', 'Manajemen Pengguna')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h4 class="fw-bold mb-1">Manajemen Akun Guru & Admin</h4>
        <p class="text-muted">Kelola hak akses dan akun pengguna sistem Codero.</p>
    </div>
    <div class="col-md-6 text-md-end">
        <a href="{{ route('admin.user.create') }}" class="btn btn-success rounded-3 px-4 shadow-sm">
            ➕ Tambah Pengguna Baru
        </a>
    </div>
</div>

@if (session('success'))
<div class="alert alert-success border-0 shadow-sm"><strong>Berhasil!</strong> {{ session('success') }}</div>
@endif

@if ($errors->any())
<div class="alert alert-danger border-0 shadow-sm">
    <ul class="mb-0">@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
</div>
@endif

<div class="card saas-card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form action="{{ route('admin.user.index') }}" method="GET" class="mb-0">
            <div class="row g-2 align-items-center">

                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted">🔍</span>
                        <input type="text" name="search" class="form-control border-start-0 bg-light" placeholder="Cari Nama / Email..." value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-md-3">
                    <select name="role" class="form-select border-light shadow-sm text-secondary">
                        <option value="">-- Semua Hak Akses --</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>👑 Administrator</option>
                        <option value="teacher" {{ request('role') == 'teacher' ? 'selected' : '' }}>👨‍🏫 Teacher / Guru</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <select name="status" class="form-select border-light shadow-sm text-secondary">
                        <option value="">-- Semua Status --</option>
                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>🟢 Akun Aktif</option>
                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>⚫ Akun Nonaktif</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100 fw-semibold shadow-sm" style="background-color: #4f46e5; border: none;">Filter</button>

                        @if(request()->hasAny(['search', 'role', 'status']))
                            <a href="{{ route('admin.user.index') }}" class="btn btn-light border text-danger shadow-sm" title="Reset Semua Filter">✖</a>
                        @endif
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>

<div class="card saas-card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-center">
                <thead class="bg-light text-muted">
                    <tr>
                        <th class="py-3 text-start ps-4">Nama Pengguna</th>
                        <th class="py-3">Email Akses</th>
                        <th class="py-3">Hak Akses</th>
                        <th class="py-3">Status</th> <th class="py-3 text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $item)
                    <tr>
                        <td class="text-start ps-4 fw-bold text-dark">{{ $item->name }}</td>
                        <td class="text-muted">{{ $item->email }}</td>
                        <td>
                            @if($item->role == 'admin')
                                <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3">👑 Admin</span>
                            @else
                                <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3">👨‍🏫 Teacher</span>
                            @endif
                        </td>
                        <td>
                            @if($item->is_active)
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">🟢 Aktif</span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3">⚫ Nonaktif</span>
                            @endif
                        </td>
                        <td class="pe-4 text-end">
                            <form action="{{ route('admin.user.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin mengubah status akun ini?');">
                                @csrf @method('DELETE')
                                <a href="{{ route('admin.user.edit', $item->id) }}" class="btn btn-sm btn-light border shadow-sm me-1">✏️ Edit</a>

                                <button type="submit" class="btn btn-sm {{ $item->is_active ? 'btn-outline-danger' : 'btn-outline-success' }} shadow-sm">
                                    {{ $item->is_active ? '🚫 Nonaktifkan' : '✅ Aktifkan' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-5 text-muted text-center">Belum ada data pengguna yang terdaftar.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="mt-3">{{ $users->links() }}</div>
@endsection
