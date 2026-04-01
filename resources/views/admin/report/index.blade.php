@extends('layouts.master')

@section('title', 'Cetak Laporan Klastering')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-8">
        <h4 class="fw-bold mb-1">Cetak Laporan Klastering</h4>
        <p class="text-muted">Filter dan unduh hasil pengelompokan K-Means dalam format PDF atau Excel.</p>
    </div>
</div>

<!-- KOTAK FILTER -->
<div class="card saas-card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form action="{{ route('admin.report.index') }}" method="GET" class="mb-0">
            <div class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">📅</span>
                        <input type="date" name="date" class="form-control border-start-0 bg-light" value="{{ request('date') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <select name="k_value" class="form-select border-light shadow-sm">
                        <option value="">-- Semua Jumlah Klaster --</option>
                        <option value="2" {{ request('k_value') == '2' ? 'selected' : '' }}>2 Klaster</option>
                        <option value="3" {{ request('k_value') == '3' ? 'selected' : '' }}>3 Klaster</option>
                        <option value="4" {{ request('k_value') == '4' ? 'selected' : '' }}>4 Klaster</option>
                        <option value="5" {{ request('k_value') == '5' ? 'selected' : '' }}>5 Klaster</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100 fw-bold shadow-sm" style="background-color: #4f46e5; border:none;">Filter</button>
                        @if(request()->hasAny(['date', 'k_value']))
                            <a href="{{ route('admin.report.index') }}" class="btn btn-light border text-danger shadow-sm">✖</a>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- TABEL CETAK -->
<div class="card saas-card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-center">
                <thead class="bg-light text-muted" style="text-transform: uppercase; font-size: 0.85rem;">
                    <tr>
                        <th class="py-3 text-start ps-4">Tanggal Eksekusi</th>
                        <th class="py-3">Info Klaster</th>
                        <th class="py-3">Validasi DBI</th>
                        <th class="py-3 text-end pe-4">Aksi Cetak</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                    <tr>
                        <td class="text-start ps-4 fw-bold text-dark">{{ $log->created_at->format('d F Y') }}<br><small class="text-muted fw-normal">{{ $log->created_at->format('H:i') }} WIB</small></td>
                        <td><span class="badge bg-primary bg-opacity-10 text-primary px-3 rounded-pill">{{ $log->k_value }} Klaster</span></td>
                        <td><span class="badge bg-light text-dark border">{{ number_format($log->dbi_score, 4) }}</span></td>
                        <td class="text-end pe-4">
                            <a href="{{ route('admin.report.excel', $log->id) }}" class="btn btn-sm btn-success shadow-sm me-1">📊 Excel</a>
                            <a href="{{ route('admin.report.pdf', $log->id) }}" class="btn btn-sm btn-danger shadow-sm">📄 PDF</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-5 text-muted">Belum ada data laporan yang tersedia.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="mt-3">{{ $logs->links() }}</div>
@endsection
