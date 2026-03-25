@extends('layouts.master')

@section('title', 'Ruang Guru')


@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h4 class="fw-bold mb-1">Selamat Bertugas</h4>
        <p class="text-muted">Kelola nilai evaluasi siswa secara objektif di panel ini.</p>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card saas-card p-3">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-3 me-3">
                        <span class="fs-4">📝</span>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1">Penilaian Siswa</h5>
                        <p class="text-muted mb-0" style="font-size: 0.9rem;">Input dan perbarui nilai berdasarkan kriteria yang telah ditetapkan oleh Admin.</p>
                    </div>
                </div>
                <hr class="text-muted my-4">
                <a href="#" class="btn btn-outline-primary rounded-3 px-4">Mulai Mengisi Nilai</a>
            </div>
        </div>
    </div>
</div>
@endsection
