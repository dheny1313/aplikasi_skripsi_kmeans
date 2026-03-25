@extends('layouts.master')

@section('title', 'Dashboard Overview')


@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h4 class="fw-bold mb-1">Selamat Datang di Ruang Kontrol</h4>
        <p class="text-muted">Pantau aktivitas pengelompokan siswa dan kelola data master dari sini.</p>
    </div>
</div>

<div class="row g-4 mb-5">
    <div class="col-md-4">
        <div class="card saas-card h-100 p-3">
            <div class="card-body">
                <h6 class="text-muted fw-semibold mb-3">Total Siswa Aktif</h6>
                <h2 class="fw-bold text-dark mb-0">0 <span class="fs-6 text-muted fw-normal">Siswa</span></h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card saas-card h-100 p-3">
            <div class="card-body">
                <h6 class="text-muted fw-semibold mb-3">Kriteria Penilaian</h6>
                <h2 class="fw-bold text-dark mb-0">0 <span class="fs-6 text-muted fw-normal">Kriteria</span></h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card saas-card h-100 p-3" style="background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);">
            <div class="card-body text-white">
                <h6 class="fw-semibold mb-3 text-white-50">Riwayat Perhitungan</h6>
                <h2 class="fw-bold mb-0">0 <span class="fs-6 fw-normal text-white-50">Kali Eksekusi</span></h2>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card saas-card p-2">
            <div class="card-body">
                <h5 class="fw-bold mb-3">Langkah Selanjutnya</h5>
                <p class="text-muted">Untuk memulai proses K-Means, pastikan Anda telah mengisi <strong>Data Kriteria</strong> dan mendaftarkan <strong>Data Siswa</strong> terlebih dahulu.</p>
                <button class="btn btn-primary mt-2 px-4 rounded-3" style="background-color: #4f46e5; border: none;">+ Tambah Data Siswa</button>
            </div>
        </div>
    </div>
</div>
@endsection
