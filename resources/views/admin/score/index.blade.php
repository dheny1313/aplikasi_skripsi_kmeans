@extends('layouts.master')

@section('title', 'Penilaian Siswa')

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <h4 class="fw-bold mb-1">Manajemen Nilai Siswa</h4>
        <p class="text-muted">Pantau atau import nilai evaluasi secara massal.</p>
    </div>
    <div class="col-md-6 text-md-end">
        <button type="button" class="btn btn-success rounded-3 px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalImportScore">
            📊 Import Nilai Excel
        </button>
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
        <form action="{{ route('admin.score.index') }}" method="GET" class="mb-0">
            <div class="row g-3">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted">🔍</span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Cari Nama / NIS Siswa..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <select name="status" class="form-select text-secondary">
                        <option value="">-- Semua Status Penilaian --</option>
                        <option value="belum" {{ request('status') == 'belum' ? 'selected' : '' }}>🔴 Belum Ada Nilai (Kosong)</option>
                        <option value="sudah" {{ request('status') == 'sudah' ? 'selected' : '' }}>🟢 Sudah Mulai Dinilai</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100 fw-semibold" style="background-color: #4f46e5; border: none;">Filter</button>
                        @if(request()->has('search') || request()->has('status'))
                            <a href="{{ route('admin.score.index') }}" class="btn btn-light border text-danger" title="Reset Filter">✖</a>
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
                            @php
                                // Menghitung ada berapa guru (teacher_id unik) yang sudah menilai siswa ini
                                $totalTeachersGraded = $item->scores->groupBy('teacher_id')->count();
                            @endphp

                            @if($totalTeachersGraded > 0)
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3">
                                    Dinilai oleh {{ $totalTeachersGraded }} Guru
                                </span>
                            @else
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3">
                                    Belum Ada Nilai
                                </span>
                            @endif
                        </td>
                        <td class="pe-4 text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.score.show', $item->id) }}" class="btn btn-sm btn-info text-white px-3 shadow-sm d-flex align-items-center">
                                    🔍 Detail
                                </a>

                                <a href="{{ route('admin.score.edit', $item->id) }}" class="btn btn-sm btn-primary px-3 shadow-sm d-flex align-items-center" style="background-color: #4f46e5; border: none;">
                                    ✏️ Edit
                                </a>
                            </div>
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

<div class="modal fade" id="modalImportScore" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Import Nilai Siswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.score.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info border-0 shadow-sm" style="font-size: 0.9rem;">
                        <strong>Format Excel Wajib:</strong><br>
                        Kolom pertama harus bernama <b>nis</b>.<br>
                        Kolom berikutnya gunakan <b>Kode Kriteria</b> (cth: C1, C2).<br>
                        <i>Isi dengan angka skala 1 sampai 5.</i><br><br>
                        Contoh Header: | <b>nis</b> | <b>c1</b> | <b>c2</b> |
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-bold">Data Ini Milik Guru Siapa?</label>
                        <select name="teacher_id" class="form-select border-success">
                            <option value="">-- Import Sebagai Admin (Diri Saya Sendiri) --</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}">👨‍🏫 {{ $teacher->name }}</option>
                            @endforeach
                        </select>
                        <small class="text-success mt-1 d-block"><i>* Pilih nama guru jika Anda mengunggah file ini mewakili mereka.</i></small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted fw-semibold">Pilih File Nilai (.xlsx)</label>
                        <input type="file" name="file_excel" class="form-control form-control-lg" accept=".xlsx, .xls, .csv" required>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Mulai Import</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
