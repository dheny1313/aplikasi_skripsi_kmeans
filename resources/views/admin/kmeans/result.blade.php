@extends('layouts.master')

@section('title', 'Hasil Detail K-Means')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-8">
        <h4 class="fw-bold mb-1">Hasil Klastering K-Means</h4>
        <p class="text-muted mb-1">Detail pengelompokan siswa dan riwayat pergerakan centroid.</p>
        <span class="badge bg-light text-secondary border px-3 py-2"><i class="fas fa-info-circle me-1"></i> {{ $log->description }}</span>
    </div>
    <div class="col-md-4 text-md-end">
        <a href="{{ route('admin.kmeans.index') }}" class="btn btn-light border shadow-sm px-4">🔙 Kembali ke Master</a>
    </div>
</div>

@if (session('success'))
<div class="alert alert-success border-0 shadow-sm">
    <strong>Berhasil!</strong> {{ session('success') }}
</div>
@endif

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card saas-card bg-primary text-white border-0 h-100">
            <div class="card-body">
                <p class="mb-1 opacity-75">Jumlah Klaster (K)</p>
                <h2 class="fw-bold mb-0">{{ $log->k_value }} Klaster</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card saas-card bg-dark text-white border-0 h-100">
            <div class="card-body">
                <p class="mb-1 opacity-75">Skor Validasi DBI</p>
                <h2 class="fw-bold mb-0">{{ number_format($log->dbi_score, 4) }}</h2>
                <small class="opacity-75">
                    *Mendekati 0 berarti semakin valid dan padat.
                </small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card saas-card bg-light border-0 h-100">
            <div class="card-body">

                {{-- <h2 class="fw-bold text-dark mb-0">{{ count($history) }} Iterasi</h2> --}}
                <p class="mb-1 text-muted">Total Iterasi Konvergen</p>
                <h2 class="fw-bold text-dark mb-0">{{ $log->total_iterations }} Iterasi</h2>
                <small class="text-muted">Centroid berhenti bergerak di iterasi ini.</small>

            </div>
        </div>
    </div>
</div>

<h5 class="fw-bold mb-3 mt-5">👥 Daftar Anggota Klaster</h5>
<div class="row">
    @foreach($log->results->groupBy('cluster_number') as $clusterNumber => $members)
    <div class="col-md-4 mb-4">
        <div class="card saas-card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                <h6 class="fw-bold text-primary mb-0">Klaster {{ $clusterNumber }}</h6>
                <span class="badge bg-light text-dark border mt-2">{{ $members->count() }} Siswa</span>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush" style="font-size: 0.9rem;">
                    @foreach($members as $member)
                        <li class="list-group-item px-0 py-2 border-light text-muted">
                            <strong class="text-dark">{{ $member->snapshot_data['nis'] ?? '-' }}</strong> - {{ $member->snapshot_data['name'] ?? 'Data Tidak Diketahui' }}
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endforeach
</div>

@if(!empty($history))
    @if(isset($history['initial_centroids']))
        {{-- NEW FORMAT --}}
        
        <h5 class="fw-bold mb-3 mt-5">🎯 Titik Centroid Awal (Initial Centroids)</h5>
        <div class="card saas-card border-0 shadow-sm mb-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 text-center align-middle" style="font-size: 0.9rem;">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Klaster</th>
                                <th>Dipilih Dari Siswa</th>
                                <th>Koordinat Awal (Skor Kriteria)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($history['initial_centroids'] as $ic)
                            <tr>
                                <td class="ps-4 fw-bold text-primary">C{{ $ic['cluster'] }}</td>
                                <td>
                                    <span class="fw-bold text-dark">{{ $ic['name'] }}</span>
                                    @if($ic['nis'] !== '-')
                                        <br><small class="text-muted">NIS: {{ $ic['nis'] }}</small>
                                    @endif
                                </td>
                                <td class="text-start">
                                    @foreach($ic['scores'] as $k => $v)
                                        <span class="badge bg-light text-dark border me-1 mb-1">{{ $k }}: {{ number_format($v, 2) }}</span>
                                    @endforeach
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <h5 class="fw-bold mb-3 mt-5">🔄 Riwayat Detail Iterasi</h5>
        <div class="accordion mb-4" id="accordionHistory">
            @foreach($history['iterations'] as $step)
            <div class="accordion-item border-0 shadow-sm mb-2 rounded">
                <h2 class="accordion-header" id="heading-{{ $step['iteration'] }}">
                    <button class="accordion-button {{ $loop->last ? '' : 'collapsed' }} fw-bold text-dark rounded" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $step['iteration'] }}" aria-expanded="{{ $loop->last ? 'true' : 'false' }}" aria-controls="collapse-{{ $step['iteration'] }}">
                        Iterasi Ke-{{ $step['iteration'] }}
                    </button>
                </h2>
                <div id="collapse-{{ $step['iteration'] }}" class="accordion-collapse collapse {{ $loop->last ? 'show' : '' }}" aria-labelledby="heading-{{ $step['iteration'] }}" data-bs-parent="#accordionHistory">
                    <div class="accordion-body bg-light">
                        <div class="row">
                            @foreach($step['clusters'] as $cluster)
                            <div class="col-md-4 mb-3">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-header bg-white border-bottom-0 pt-3 pb-1">
                                        <h6 class="fw-bold text-primary mb-0">Klaster {{ $cluster['cluster'] }}</h6>
                                        <span class="badge bg-secondary mt-1">{{ count($cluster['members']) }} Anggota</span>
                                    </div>
                                    <div class="card-body py-2">
                                        <p class="small fw-bold mb-1 text-muted">Anggota:</p>
                                        <div class="mb-3" style="max-height: 100px; overflow-y: auto;">
                                            <ul class="list-unstyled small mb-0">
                                                @forelse($cluster['members'] as $m)
                                                    <li class="text-truncate" title="{{ $m['name'] }}">- {{ $m['name'] }}</li>
                                                @empty
                                                    <li class="text-danger fst-italic">Kosong</li>
                                                @endforelse
                                            </ul>
                                        </div>
                                        <p class="small fw-bold mb-1 text-muted">Centroid Baru (Update):</p>
                                        <div class="">
                                            @foreach($cluster['new_centroid'] as $k => $v)
                                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle me-1 mb-1">{{ $k }}: {{ number_format($v, 2) }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <h5 class="fw-bold mb-3 mt-5">🏁 Titik Centroid Akhir (Final Centroids)</h5>
        <div class="card saas-card border-0 shadow-sm mb-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 text-center align-middle" style="font-size: 0.9rem;">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th class="ps-4">Klaster</th>
                                <th>Koordinat Akhir (Konvergen)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($history['final_centroids'] as $cIndex => $fc)
                            <tr>
                                <td class="ps-4 fw-bold text-primary fs-5">C{{ $cIndex + 1 }}</td>
                                <td class="text-start">
                                    @foreach($fc as $k => $v)
                                        <span class="badge bg-light text-dark border me-1 mb-1 fs-6">{{ $k }}: {{ number_format($v, 2) }}</span>
                                    @endforeach
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    @else
        {{-- OLD FORMAT (Fallback) --}}
        <div class="card saas-card mt-4">
            <div class="card-header bg-white pt-4 pb-2 border-bottom-0">
                <h6 class="fw-bold mb-0">🔄 Riwayat Pergerakan Centroid (Format Lama)</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0" style="font-size: 0.85rem;">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Iterasi Ke-</th>
                                <th>Posisi Titik Pusat (Centroid) per Klaster</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($history as $step)
                            <tr>
                                <td class="ps-4 fw-bold">Iterasi {{ $step['iteration'] }}</td>
                                <td>
                                    @foreach($step['centroids'] as $cIndex => $centroid)
                                        <div class="mb-1">
                                            <span class="badge bg-secondary">C{{ $cIndex + 1 }}</span>
                                            <span class="text-muted ms-1">
                                                [ {{ implode(', ', array_map(function($k, $v) { return $k.': '.round($v, 2); }, array_keys($centroid), $centroid)) }} ]
                                            </span>
                                        </div>
                                    @endforeach
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
@endif 
@endsection
