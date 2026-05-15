@extends('layouts.master')

@section('title', 'Detail Nilai Siswa')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h4 class="fw-bold mb-1">Detail Penilaian: {{ $student->name }}</h4>
        <p class="text-muted">NIS: {{ $student->student_id }} | Monitoring rincian nilai dari berbagai guru.</p>
    </div>
    <div class="col-md-4 text-md-end">
        <a href="{{ route('admin.score.index') }}" class="btn btn-light border shadow-sm px-4">🔙 Kembali</a>
    </div>
</div>

<div class="card saas-card border-0 shadow-sm">
    <div class="card-header bg-white pt-4 pb-3 border-bottom-0">
        <h6 class="fw-bold text-primary mb-0">📊 Matriks Nilai Berdasarkan Guru</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0 text-center align-middle" style="font-size: 0.95rem;">
                <thead class="bg-light">
                    <tr>
                        <th class="text-start ps-4 py-3">Nama Guru (Penilai)</th>
                        @foreach($criteria as $criterion)
                            <th title="{{ $criterion->name }}" class="py-3">{{ $criterion->code }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($scoresByTeacher as $teacherId => $teacherScores)
                        @php
                            // Ambil nama guru. Jika teacher_id null/tidak ketemu, anggap sebagai Admin
                            $teacherName = $teacherScores->first()->teacher ? $teacherScores->first()->teacher->name : 'Sistem / Admin';
                            // Ubah koleksi nilai menjadi array dengan format [criterion_id => score] agar mudah dipanggil
                            $scoreMap = $teacherScores->pluck('score', 'criterion_id')->toArray();
                        @endphp
                        <tr>
                            <td class="text-start ps-4 fw-bold text-dark py-3">
                                👨‍🏫 {{ $teacherName }}
                            </td>
                            @foreach($criteria as $criterion)
                                <td>
                                    @if(isset($scoreMap[$criterion->id]))
                                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill shadow-sm" style="font-size: 0.9rem;">
                                            {{ $scoreMap[$criterion->id] }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($criteria) + 1 }}" class="py-5 text-muted">
                                <i>Siswa ini belum menerima penilaian dari guru mana pun.</i>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection