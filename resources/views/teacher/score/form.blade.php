@extends('layouts.master')

@section('title', 'Input Nilai Siswa')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h4 class="fw-bold mb-1">Form Penilaian Siswa</h4>
        <p class="text-muted">
            Siswa: <strong class="text-dark">{{ $student->name }}</strong> (NIS: {{ $student->student_id }})
        </p>
    </div>
    <div class="col-md-4 text-md-end">
        <a href="{{ route('teacher.score.index') }}" class="btn btn-light border shadow-sm px-4">🔙 Kembali</a>
    </div>
</div>

@if ($errors->any())
<div class="alert alert-danger border-0 shadow-sm mb-4">
    <ul class="mb-0">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="row">
    <div class="col-md-8">
        <div class="card saas-card border-0 shadow-sm">
            <div class="card-body p-4">
                
                <form action="{{ route('teacher.score.update', $student->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        @forelse ($criteria as $criterion)
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold text-dark mb-1">
                                    {{ $criterion->name }} 
                                    <span class="badge bg-light text-secondary border ms-1">{{ $criterion->code }}</span>
                                </label>
                                {{-- <div class="input-group input-group-lg shadow-sm">
                                    <input type="number" 
                                           step="0.01" 
                                           name="score_{{ $criterion->id }}" 
                                           class="form-control bg-light" 
                                           placeholder="0 - 100" 
                                           min="0" max="100"
                                           value="{{ old('score_'.$criterion->id, $existingScores[$criterion->id] ?? '') }}" 
                                           required>
                                    <span class="input-group-text bg-white text-muted">/ 100</span>
                                </div> --}}
                                <select name="score_{{ $criterion->id }}" class="form-select form-select-lg bg-light shadow-sm" required>
                                    <option value="">-- Pilih Penilaian --</option>
                                    
                                    @foreach($criterion->scales as $scale)
                                        <option value="{{ $scale->scale_value }}" {{ old('score_'.$criterion->id, $existingScores[$criterion->id] ?? '') == $scale->scale_value ? 'selected' : '' }}>
                                            {{ $scale->scale_value }} - {{ $scale->description }}
                                        </option>
                                    @endforeach
                                    
                                </select>
                                   
                            </div>
                        @empty
                            <div class="col-12 text-center py-4">
                                <p class="text-danger mb-0"><strong>Peringatan:</strong> Admin belum mengatur Data Kriteria sama sekali. Hubungi Admin untuk menambahkan kriteria evaluasi.</p>
                            </div>
                        @endforelse
                    </div>

                    <hr class="text-muted my-4">
                    
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary px-5 py-2 fw-semibold" style="background-color: #4f46e5; border: none;" {{ $criteria->count() == 0 ? 'disabled' : '' }}>
                            💾 Simpan Nilai
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
    
    <div class="col-md-4 mt-4 mt-md-0">
        <div class="card saas-card bg-primary bg-opacity-10 border-0">
            <div class="card-body p-4 text-primary">
                <h6 class="fw-bold mb-3">💡 Panduan Penilaian</h6>
                <ul class="ps-3 mb-0" style="font-size: 0.9rem;">
                    <li class="mb-2">Pastikan nilai diisi dengan skala <strong>1 sampai 5</strong>.</li>
                    <li class="mb-2">Perhatikan Skala penilaian dan deskripsi penilaian</li>
                    <li>Semua kotak kriteria <strong>wajib</strong> diisi agar algoritma K-Means bisa memproses data siswa ini secara akurat.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection