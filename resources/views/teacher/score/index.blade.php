@extends('layouts.master')

@section('title', 'Penilaian Siswa')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h4 class="fw-bold mb-1">Daftar Siswa Aktif</h4>
        <p class="text-muted">Pilih siswa untuk memasukkan atau memperbarui nilai evaluasi.</p>
    </div>
</div>

@if (session('success'))
<div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
    <strong>Berhasil!</strong> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="card saas-card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form action="{{ route('teacher.score.index') }}" method="GET" class="mb-0">
            <div class="row g-3">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted">🔍</span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Cari Nama / NIS Siswa..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <select name="status" class="form-select text-secondary">
                        <option value="">-- Semua Status Penilaian Saya --</option>
                        <option value="belum" {{ request('status') == 'belum' ? 'selected' : '' }}>🔴 Belum Saya Nilai</option>
                        <option value="sudah" {{ request('status') == 'sudah' ? 'selected' : '' }}>🟢 Sudah Saya Nilai</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100 fw-semibold" style="background-color: #4f46e5; border: none;">Filter</button>
                        @if(request()->has('search') || request()->has('status'))
                            <a href="{{ route('teacher.score.index') }}" class="btn btn-light border text-danger" title="Reset Filter">✖</a>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card saas-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">
                    <tr>
                        <th class="ps-4 py-3">No</th>
                        <th class="py-3">NIS</th>
                        <th class="py-3">Nama Siswa</th>
                        <th class="py-3">Status Penilaian</th>
                        <th class="pe-4 py-3 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody style="font-size: 0.95rem;">
                    @forelse ($students as $index => $item)
                    <tr>
                        <td class="ps-4 text-muted">{{ $students->firstItem() + $index }}</td>
                        <td class="fw-semibold">{{ $item->student_id }}</td>
                        <td>{{ $item->name }}</td>
                        <td>
                            @if($item->scores->count() == $totalCriteria && $totalCriteria > 0)
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3">Sudah Lengkap</span>
                            
                            @elseif($item->scores->count() > 0 && $item->scores->count() < $totalCriteria)
                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill px-3">Belum Lengkap</span>  
                            @else
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3">Belum Dinilai</span>
                            @endif
                        </td>
                        
                        <td class="pe-4 text-end">
                            <a href="{{ route('teacher.score.edit', $item->id) }}" class="btn btn-sm btn-primary px-3 shadow-sm" style="background-color: #4f46e5; border: none;">
                                {{ $item->scores->count() > 0 ? 'Edit Nilai' : 'Input Nilai' }}
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">Belum ada data siswa aktif.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-between align-items-center mt-3 px-3 pb-2">
            <div class="text-muted" style="font-size: 0.85rem;">
                Menampilkan {{ $students->firstItem() ?? 0 }} - {{ $students->lastItem() ?? 0 }} dari {{ $students->total() }} siswa.
            </div>
            <div>
                {{ $students->links() }}
            </div>
        </div>
    </div>
</div>
@endsection