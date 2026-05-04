@extends('layouts.master')

@section('title', 'Data Normalisasi')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-8">
        <h4 class="fw-bold mb-1">Menu Data Normalisasi</h4>
        <p class="text-muted">Kelola dan sinkronkan data nilai siswa sebelum masuk ke tahap perhitungan K-Means.</p>
    </div>
    <div class="col-md-4 text-md-end">
        <!-- Tombol Pemicu Sinkronisasi -->
        <form action="{{ route('admin.data.sync') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-primary shadow-sm px-4">
                🔄 Update Data Normalisasi
            </button>
        </form>
    </div>
</div>

@if (session('success'))
<div class="alert alert-success border-0 shadow-sm mb-4">
    <strong>Berhasil!</strong> {{ session('success') }}
</div>
@endif

@if (session('error'))
<div class="alert alert-danger border-0 shadow-sm mb-4">
    <strong>Peringatan!</strong> {{ session('error') }}
</div>
@endif

<div class="card saas-card border-0 shadow-sm mb-4">
    <div class="card-header bg-white pt-4 pb-3 border-bottom-0">
        <h6 class="fw-bold text-primary mb-0">📋 Tabel Data Siap Hitung (K-Means)</h6>
    </div>
    <div class="card-body p-0">
        @if($normalizedData->isEmpty())
            <div class="text-center py-5">
                <div class="mb-3">
                    <span class="fs-1">📂</span>
                </div>
                <h5 class="text-muted mb-2 fw-bold">Data Belum Dinormalisasi</h5>
                <p class="text-muted mb-0">Silakan klik tombol <strong>"Update Data Normalisasi"</strong> di sudut kanan atas untuk menarik data.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover table-borderless align-middle mb-0" style="font-size: 0.9rem;">
                    <thead class="bg-light border-bottom">
                        <tr>
                            <th class="ps-4">No</th>
                            <th>ID Siswa</th>
                            <th>Nama Siswa</th>
                            <th>Data V (Siap Hitung)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($normalizedData as $index => $data)
                        <tr class="border-bottom">
                            <td class="ps-4 text-muted">{{ $index + 1 }}</td>
                            <td class="fw-bold text-dark">{{ $data->student->student_id ?? '-' }}</td>
                            <td>{{ $data->student->name ?? 'Data Siswa Dihapus' }}</td>
                            <td>
                                <!-- Tampilan Nilai V Dipercantik Menggunakan Badge -->
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($data->normalized_data as $key => $value)
                                        <span class="badge bg-light text-dark border px-2 py-1">
                                            {{ $key }}: <span class="fw-bold text-primary">{{ $value }}</span>
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
