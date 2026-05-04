@extends('layouts.master')

@section('title', 'Laporan Hasil Klastering')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h4 class="fw-bold mb-1">Laporan Hasil Klastering Siswa</h4>
        <p class="text-muted">Lihat hasil pengelompokan siswa berdasarkan algoritma K-Means yang telah dieksekusi oleh Admin.</p>
    </div>
</div>

<!-- KOTAK FILTER PENCARIAN -->
<div class="card saas-card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form action="{{ route('teacher.kmeans.index') }}" method="GET" class="mb-0">
            <div class="row g-2 align-items-center">

                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted" title="Cari berdasarkan tanggal">📅</span>
                        <input type="date" name="date" class="form-control border-start-0 bg-light" value="{{ request('date') }}">
                    </div>
                </div>

                <div class="col-md-4">
                    <select name="k_value" class="form-select border-light shadow-sm text-secondary">
                        <option value="">-- Semua Jumlah Klaster --</option>
                        <option value="2" {{ request('k_value') == '2' ? 'selected' : '' }}>2 Klaster</option>
                        <option value="3" {{ request('k_value') == '3' ? 'selected' : '' }}>3 Klaster</option>
                        <option value="4" {{ request('k_value') == '4' ? 'selected' : '' }}>4 Klaster</option>
                        <option value="5" {{ request('k_value') == '5' ? 'selected' : '' }}>5 Klaster</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100 fw-semibold shadow-sm" style="background-color: #4f46e5; border: none;">Filter</button>

                        @if(request()->hasAny(['date', 'k_value']))
                            <a href="{{ route('teacher.kmeans.index') }}" class="btn btn-light border text-danger shadow-sm" title="Reset Semua Filter">✖</a>
                        @endif
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>

<div class="card saas-card border-0 shadow-sm">
    <div class="card-header bg-white pt-4 pb-2 border-bottom-0">
        <h6 class="fw-bold mb-0">🕒 Daftar Riwayat Klastering</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-center">
                <thead class="bg-light text-muted" style="font-size: 0.85rem; text-transform: uppercase;">
                    <tr>
                        <th class="py-3 text-start ps-4">Tanggal Ditetapkan</th>
                        <th class="py-3">Jumlah Klaster</th>
                        <th class="py-3 text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody style="font-size: 0.95rem;">
                    @forelse ($logs as $log)
                    <tr>
                        <td class="text-start ps-4">{{ $log->created_at->format('d M Y, H:i') }}</td>
                        <td class="fw-bold text-primary">{{ $log->k_value }} Klaster</td>
                        <td class="text-end pe-4">
                            <a href="{{ route('teacher.kmeans.result', $log->id) }}" class="btn btn-sm btn-primary shadow-sm" style="background-color: #4f46e5; border: none;">Lihat Anggota 👥</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-5 text-muted">Belum ada laporan klastering yang diterbitkan oleh Admin.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
