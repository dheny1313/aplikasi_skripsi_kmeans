@extends('layouts.master')

@section('title', 'Master Data Siswa')


@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h4 class="fw-bold mb-1">Daftar Siswa</h4>
        <p class="text-muted">Kelola data induk siswa yang akan dievaluasi.</p>
    </div>
    <div class="col-md-4 text-md-end">
        <button type="button" class="btn btn-success rounded-3 px-3 me-2" style="border: none;" data-bs-toggle="modal" data-bs-target="#modalImport">
            📊 Import Excel
        </button>
        <button type="button" class="btn btn-primary rounded-3 px-4 mt-2" style="background-color: #4f46e5; border: none;" data-bs-toggle="modal" data-bs-target="#modalTambah">
            + Tambah Siswa
        </button>
    </div>
</div>

@if (session('success'))
<div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
    <strong>Berhasil!</strong> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if ($errors->any())
<div class="alert alert-danger border-0 shadow-sm">
    <ul class="mb-0">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif


{{--ui filter  --}}
<div class="card saas-card mb-4 bg-white">
    <div class="card-body p-3">
        <form action="{{ route('admin.student.index') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label text-muted fw-semibold mb-1" style="font-size: 0.85rem;">Pencarian Bebas</label>
                <input type="text" name="search" class="form-control" placeholder="Ketik ID atau Nama Siswa..." value="{{ request('search') }}">
            </div>
            
            <div class="col-md-3">
                <label class="form-label text-muted fw-semibold mb-1" style="font-size: 0.85rem;">Jenis Kelamin</label>
                <select name="gender" class="form-select">
                    <option value="">Semua Jenis Kelamin</option>
                    <option value="L" {{ request('gender') == 'L' ? 'selected' : '' }}>Laki-laki (L)</option>
                    <option value="P" {{ request('gender') == 'P' ? 'selected' : '' }}>Perempuan (P)</option>
                </select>
            </div>
            
            <div class="col-md-3">
                <label class="form-label text-muted fw-semibold mb-1" style="font-size: 0.85rem;">Status Akun</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Non-aktif</option>
                </select>
            </div>
            
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100 fw-semibold" style="background-color: #4f46e5; border: none;">Cari</button>
                <a href="{{ route('admin.student.index') }}" class="btn btn-light w-100 border fw-semibold text-muted">Reset</a>
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
                        <th class="py-3">ID / NIS</th>
                        <th class="py-3">Nama Siswa</th>
                        <th class="py-3">L/P</th>
                        <th class="py-3">Status</th>
                        <th class="pe-4 py-3 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody style="font-size: 0.95rem;">
                    @forelse ($students as $index => $item)
                    <tr>
                        <td class="ps-4 text-muted">{{ $index + 1 }}</td>
                        <td class="fw-semibold">{{ $item->student_id }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->gender }}</td>
                        <td>
                            @if($item->is_active)
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3">Aktif</span>
                            @else
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3">Non-aktif</span>
                            @endif
                        </td>
                        <td class="pe-4 text-end">
                            <button class="btn btn-sm btn-light border shadow-sm me-1" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $item->id }}">Edit</button>
                            
                            <form action="{{ route('admin.student.toggle', $item->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm {{ $item->is_active ? 'btn-outline-danger' : 'btn-outline-success' }} shadow-sm">
                                    {{ $item->is_active ? 'Non-aktifkan' : 'Aktifkan' }}
                                </button>
                            </form>
                        </td>
                    </tr>

                    <div class="modal fade" id="modalEdit{{ $item->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow">
                                <div class="modal-header border-bottom-0 pb-0">
                                    <h5 class="modal-title fw-bold">Edit Data Siswa</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="{{ route('admin.student.update', $item->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label text-muted fw-semibold" style="font-size: 0.9rem;">ID / NIS</label>
                                            <input type="text" name="student_id" class="form-control form-control-lg" value="{{ $item->student_id }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label text-muted fw-semibold" style="font-size: 0.9rem;">Nama Lengkap</label>
                                            <input type="text" name="name" class="form-control form-control-lg" value="{{ $item->name }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label text-muted fw-semibold" style="font-size: 0.9rem;">Jenis Kelamin</label>
                                            <select name="gender" class="form-select form-select-lg" required>
                                                <option value="L" {{ $item->gender == 'L' ? 'selected' : '' }}>Laki-laki (L)</option>
                                                <option value="P" {{ $item->gender == 'P' ? 'selected' : '' }}>Perempuan (P)</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-top-0 pt-0">
                                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary px-4" style="background-color: #4f46e5; border: none;">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">Belum ada data siswa.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- pagination  --}}
        <div class="d-flex justify-content-between align-items-center mt-3 px-3 pb-2">
            <div class="text-muted" style="font-size: 0.85rem;">
                Menampilkan data ke {{ $students->firstItem() ?? 0 }} sampai {{ $students->lastItem() ?? 0 }} dari total {{ $students->total() }} siswa.
            </div>
            <div>
                {{ $students->links() }}
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Tambah Siswa Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.student.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-muted fw-semibold" style="font-size: 0.9rem;">ID / NIS</label>
                        <input type="text" name="student_id" class="form-control form-control-lg" placeholder="Contoh: NIS202601" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted fw-semibold" style="font-size: 0.9rem;">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control form-control-lg" placeholder="Masukkan nama siswa" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted fw-semibold" style="font-size: 0.9rem;">Jenis Kelamin</label>
                        <select name="gender" class="form-select form-select-lg" required>
                            <option value="">-- Pilih Jenis Kelamin --</option>
                            <option value="L">Laki-laki (L)</option>
                            <option value="P">Perempuan (P)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4" style="background-color: #4f46e5; border: none;">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- modal import data siswa excel --}}
<div class="modal fade" id="modalImport" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Import Data Siswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.student.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info border-0 shadow-sm" style="font-size: 0.9rem;">
                        <strong>Format Excel Wajib:</strong><br>
                        Baris pertama (Header) harus berisi teks persis seperti ini:<br>
                        Kolom A: <b>nis</b> <br>
                        Kolom B: <b>nama_siswa</b> <br>
                        Kolom C: <b>jk</b> <i>(Isi dengan huruf L atau P)</i>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted fw-semibold">Pilih File (.xlsx / .csv)</label>
                        <input type="file" name="file_excel" class="form-control form-control-lg" accept=".xlsx, .xls, .csv" required>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success" style="border: none;">Mulai Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection