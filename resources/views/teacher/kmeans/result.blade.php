@extends('layouts.master')

@section('title', 'Detail Klaster Siswa')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-8">
        <h4 class="fw-bold mb-1">Detail Anggota Klaster</h4>
        <p class="text-muted">Hasil pengelompokan K-Means tanggal {{ $log->created_at->format('d F Y') }}</p>
    </div>
    <div class="col-md-4 text-md-end">
        <a href="{{ route('teacher.kmeans.index') }}" class="btn btn-light border shadow-sm px-4">🔙 Kembali ke Daftar</a>
    </div>
</div>

<div class="row">
    @foreach($log->results->groupBy('cluster_number') as $clusterNumber => $members)
    <div class="col-md-4 mb-4">
        <div class="card saas-card border-0 shadow-sm h-100 border-top border-primary border-4">
            <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                <h5 class="fw-bold text-dark mb-0">Klaster {{ $clusterNumber }}</h5>
                <span class="badge bg-primary bg-opacity-10 text-primary mt-2 mb-2">{{ $members->count() }} Siswa</span>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush" style="font-size: 0.9rem;">
                    @foreach($members as $member)
                        <li class="list-group-item px-0 py-2 border-light text-muted d-flex justify-content-between align-items-center">
                            <span>
                                <strong class="text-dark">{{ $member->snapshot_data['name'] ?? 'Data Tidak Diketahui' }}</strong><br>
                                <small style="font-size: 0.75rem;">NIS: {{ $member->snapshot_data['nis'] ?? '-' }}</small>
                            </span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection
