@extends('layouts.master')

@section('title', 'Master Data Kriteria')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h4 class="fw-bold mb-1">Daftar Kriteria</h4>
        <p class="text-muted">Kelola kriteria yang akan digunakan sebagai variabel perhitungan K-Means.</p>
    </div>
    <div class="col-md-4 text-md-end">
        <button type="button" class="btn btn-primary rounded-3 px-4" style="background-color: #4f46e5; border: none;" data-bs-toggle="modal" data-bs-target="#modalTambah">
            + Tambah Kriteria
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

<div class="card saas-card mb-4 bg-white">
    <div class="card-body p-3">
        <form action="{{ route('admin.criteria.index') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-10">
                <label class="form-label text-muted fw-semibold mb-1" style="font-size: 0.85rem;">Pencarian Kriteria</label>
                <input type="text" name="search" class="form-control" placeholder="Ketik Kode (Misal: C1) atau Nama Kriteria..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100 fw-semibold" style="background-color: #4f46e5; border: none;">Cari</button>
                <a href="{{ route('admin.criteria.index') }}" class="btn btn-light w-100 border fw-semibold text-muted">Reset</a>
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
                        <th class="py-3">Kode</th>
                        <th class="py-3">Nama Kriteria</th>
                        {{-- <th class="py-3">Bobot</th> --}}
                        <th class="pe-4 py-3 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody style="font-size: 0.95rem;">
                    @forelse ($criteria as $index => $item)
                    <tr>
                        <td class="ps-4 text-muted">{{ $criteria->firstItem() + $index }}</td>
                        <td class="fw-semibold">
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1">{{ $item->code }}</span>
                        </td>
                        <td>{{ $item->name }}</td>
                        {{-- <td>{{ $item->weight }}</td> --}}
                        <td class="pe-4 text-end">
                            <button class="btn btn-sm btn-light border shadow-sm me-1" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $item->id }}">Edit</button>
                            <button class="btn btn-sm btn-outline-danger shadow-sm" data-bs-toggle="modal" data-bs-target="#modalHapus{{ $item->id }}">Hapus</button>
                        </td>
                    </tr>

                    <div class="modal fade" id="modalEdit{{ $item->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow">
                                <div class="modal-header border-bottom-0 pb-0">
                                    <h5 class="modal-title fw-bold">Edit Kriteria</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="{{ route('admin.criteria.update', $item->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label text-muted fw-semibold">Kode</label>
                                                <input type="text" name="code" class="form-control" value="{{ $item->code }}" required>
                                            </div>
                                            <div class="col-md-8 mb-3">
                                                <label class="form-label text-muted fw-semibold">Nama Kriteria</label>
                                                <input type="text" name="name" class="form-control" value="{{ $item->name }}" required>
                                            </div>
                                        </div>

                                        <input type="hidden" name="weight" value="{{ $item->weight }}">

                                        <hr class="text-muted my-3">
                                        <h6 class="fw-bold text-primary mb-3">Rubrik Penilaian (Deskripsi Skala 1-5)</h6>
                                        
                                        <div class="mb-2">
                                            <label style="font-size: 0.85rem;" class="fw-semibold">Skala 1</label>
                                            <input type="text" name="scale_1" class="form-control form-control-sm" value="{{ $item->scales->where('scale_value', 1)->first()->description ?? '' }}" required>
                                        </div>
                                        <div class="mb-2">
                                            <label style="font-size: 0.85rem;" class="fw-semibold">Skala 2</label>
                                            <input type="text" name="scale_2" class="form-control form-control-sm" value="{{ $item->scales->where('scale_value', 2)->first()->description ?? '' }}" required>
                                        </div>
                                        <div class="mb-2">
                                            <label style="font-size: 0.85rem;" class="fw-semibold">Skala 3</label>
                                            <input type="text" name="scale_3" class="form-control form-control-sm" value="{{ $item->scales->where('scale_value', 3)->first()->description ?? '' }}" required>
                                        </div>
                                        <div class="mb-2">
                                            <label style="font-size: 0.85rem;" class="fw-semibold">Skala 4</label>
                                            <input type="text" name="scale_4" class="form-control form-control-sm" value="{{ $item->scales->where('scale_value', 4)->first()->description ?? '' }}" required>
                                        </div>
                                        <div class="mb-2">
                                            <label style="font-size: 0.85rem;" class="fw-semibold">Skala 5</label>
                                            <input type="text" name="scale_5" class="form-control form-control-sm" value="{{ $item->scales->where('scale_value', 5)->first()->description ?? '' }}" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-top-0 pt-0">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary" style="background-color: #4f46e5; border: none;">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="modalHapus{{ $item->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow">
                                <div class="modal-header border-bottom-0 pb-0">
                                    <h5 class="modal-title fw-bold text-danger">Hapus Kriteria?</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    Apakah Anda yakin ingin menghapus kriteria <strong>{{ $item->name }} ({{ $item->code }})</strong>? <br><br>
                                    <span class="text-danger" style="font-size: 0.85rem;"><i class="fw-bold">Peringatan:</i> Menghapus kriteria ini akan ikut menghapus semua nilai siswa yang terkait dengan kriteria ini.</span>
                                </div>
                                <div class="modal-footer border-top-0 pt-0">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                    <form action="{{ route('admin.criteria.destroy', $item->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Ya, Hapus Kriteria</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">Belum ada data kriteria.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-between align-items-center mt-3 px-3 pb-2">
            <div class="text-muted" style="font-size: 0.85rem;">
                Menampilkan {{ $criteria->firstItem() ?? 0 }} sampai {{ $criteria->lastItem() ?? 0 }} dari {{ $criteria->total() }} kriteria.
            </div>
            <div>
                {{ $criteria->links() }}
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Tambah Kriteria Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.criteria.store') }}" method="POST">
                @csrf
               <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted fw-semibold">Kode Kriteria</label>
                            <input type="text" name="code" class="form-control" placeholder="Contoh: C1" required>
                        </div>
                        <div class="col-md-8 mb-3">
                            <label class="form-label text-muted fw-semibold">Nama Kriteria</label>
                            <input type="text" name="name" class="form-control" placeholder="Contoh: Pemahaman Algoritma" required>
                        </div>
                    </div>

                    <input type="hidden" name="weight" value="1">

                    <hr class="text-muted my-3">
                    <h6 class="fw-bold text-primary mb-3">Rubrik Penilaian (Deskripsi Skala 1-5)</h6>
                    
                    <div class="mb-2">
                        <label style="font-size: 0.85rem;" class="fw-semibold">Skala 1 (Sangat Kurang)</label>
                        <input type="text" name="scale_1" class="form-control form-control-sm" placeholder="Deskripsi jika siswa dinilai 1..." required>
                    </div>
                    <div class="mb-2">
                        <label style="font-size: 0.85rem;" class="fw-semibold">Skala 2 (Kurang)</label>
                        <input type="text" name="scale_2" class="form-control form-control-sm" placeholder="Deskripsi jika siswa dinilai 2..." required>
                    </div>
                    <div class="mb-2">
                        <label style="font-size: 0.85rem;" class="fw-semibold">Skala 3 (Cukup)</label>
                        <input type="text" name="scale_3" class="form-control form-control-sm" placeholder="Deskripsi jika siswa dinilai 3..." required>
                    </div>
                    <div class="mb-2">
                        <label style="font-size: 0.85rem;" class="fw-semibold">Skala 4 (Baik)</label>
                        <input type="text" name="scale_4" class="form-control form-control-sm" placeholder="Deskripsi jika siswa dinilai 4..." required>
                    </div>
                    <div class="mb-2">
                        <label style="font-size: 0.85rem;" class="fw-semibold">Skala 5 (Sangat Baik)</label>
                        <input type="text" name="scale_5" class="form-control form-control-sm" placeholder="Deskripsi jika siswa dinilai 5..." required>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" style="background-color: #4f46e5; border: none;">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection