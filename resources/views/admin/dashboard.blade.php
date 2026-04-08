@extends('layouts.master')
@section('title', 'Ruang Kontrol Admin')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h4 class="fw-bold mb-1 text-dark">Selamat Datang di Ruang Kontrol 🎛️</h4>
        <p class="text-muted">Pantau aktivitas pengelompokan siswa dan kelola data master dari sini.</p>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card saas-card h-100 p-3 border-0 shadow-sm" style="border-left: 5px solid #4f46e5 !important;">
            <div class="card-body">
                <h6 class="text-muted fw-semibold mb-3">Total Siswa Terdaftar</h6>
                <h2 class="fw-bold text-dark mb-0">{{ $totalStudents }} <span class="fs-6 text-muted fw-normal">Siswa</span></h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card saas-card h-100 p-3 border-0 shadow-sm" style="border-left: 5px solid #10b981 !important;">
            <div class="card-body">
                <h6 class="text-muted fw-semibold mb-3">Kriteria Penilaian</h6>
                <h2 class="fw-bold text-dark mb-0">{{ $totalCriteria }} <span class="fs-6 text-muted fw-normal">Kriteria</span></h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card saas-card h-100 p-3 border-0 shadow-sm" style="background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);">
            <div class="card-body text-white">
                <h6 class="fw-semibold mb-3 text-white-50">Riwayat Perhitungan</h6>
                <h2 class="fw-bold mb-0">{{ $totalLogs }} <span class="fs-6 fw-normal text-white-50">Kali Eksekusi</span></h2>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card saas-card p-3 border-0 shadow-sm bg-white">
            <div class="card-body">
                <h5 class="fw-bold mb-2">Langkah Selanjutnya</h5>
                <p class="text-muted">Untuk memulai proses K-Means, pastikan Anda telah mengisi data terbaru. Ingin langsung melakukan eksekusi klastering?</p>
                <a href="{{ route('admin.kmeans.index') }}" class="btn btn-primary mt-2 px-4 rounded-pill shadow-sm" style="background-color: #4f46e5; border: none;">Mulai Analisis K-Means ➔</a>
            </div>
        </div>
    </div>
</div>
@endsection
