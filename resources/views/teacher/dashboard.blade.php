@extends('layouts.master')
@section('title', 'Beranda Pengajar')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-8">
        <h4 class="fw-bold mb-1 text-dark">Selamat Datang, Pengajar! 👋</h4>
        <p class="text-muted">Pantau ringkasan data siswa dan lihat hasil alokasi klastering terbaru di Codero.</p>
    </div>
    <div class="col-md-4 text-md-end mt-3 mt-md-0">
        <span class="badge bg-light text-primary border px-3 py-2 shadow-sm rounded-pill">📅 {{ now()->translatedFormat('d F Y') }}</span>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card saas-card h-100 p-3 shadow-sm border-0" style="border-left: 5px solid #3b82f6 !important;">
            <div class="card-body">
                <h6 class="text-muted fw-semibold mb-3">Total Siswa Aktif</h6>
                <h2 class="fw-bold text-dark mb-0">{{ $totalStudents }} <span class="fs-6 text-muted fw-normal">Siswa</span></h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card saas-card h-100 p-3 shadow-sm border-0" style="border-left: 5px solid #10b981 !important;">
            <div class="card-body">
                <h6 class="text-muted fw-semibold mb-3">Arsip Laporan K-Means</h6>
                <h2 class="fw-bold text-dark mb-0">{{ $totalLogs }} <span class="fs-6 text-muted fw-normal">Dokumen</span></h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card saas-card h-100 p-3 shadow-sm border-0" style="background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);">
            <div class="card-body text-white">
                <h6 class="fw-semibold mb-3 text-white-50">Status Klaster Terkini</h6>
                @if($latestLog)
                    <h2 class="fw-bold mb-1">{{ $latestLog->k_value }} <span class="fs-6 fw-normal text-white-50">Kelompok</span></h2>
                    <p class="small text-white-50 mb-0">Diperbarui: {{ $latestLog->created_at->diffForHumans() }}</p>
                @else
                    <h5 class="fw-bold mb-0 mt-2">Belum ada data</h5>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card saas-card p-3 shadow-sm border-0 bg-white">
            <div class="card-body d-flex flex-column flex-md-row align-items-center justify-content-between">
                <div class="mb-3 mb-md-0">
                    <h5 class="fw-bold text-dark mb-2">Lihat Alokasi Siswa Terbaru</h5>
                    <p class="text-muted mb-0">Admin telah melakukan pengelompokan siswa berdasarkan algoritma K-Means. Cek daftar siswa Anda di sini.</p>
                </div>
                @if($latestLog)
                    <a href="{{ route('teacher.kmeans.result', $latestLog->id) }}" class="btn btn-primary px-4 py-2 fw-semibold rounded-pill shadow-sm" style="background-color: #4f46e5; border: none; white-space: nowrap;">Lihat Detail Klaster ➔</a>
                @else
                    <button class="btn btn-secondary px-4 py-2 fw-semibold rounded-pill" disabled>Belum Ada Laporan</button>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
