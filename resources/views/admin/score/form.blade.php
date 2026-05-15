@extends('layouts.master')

@section('title', 'Input & Edit Nilai Siswa')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h4 class="fw-bold mb-1">Form Penilaian Siswa (Hak Veto Admin)</h4>
        <p class="text-muted">
            Siswa: <strong class="text-dark">{{ $student->name }}</strong> (NIS: {{ $student->student_id }})
        </p>
    </div>
    <div class="col-md-4 text-md-end">
        <a href="{{ route('admin.score.show', $student->id) }}" class="btn btn-light border shadow-sm px-4">🔙 Kembali ke Detail</a>
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
        
        <form action="{{ route('admin.score.update', $student->id) }}" method="POST">
            @csrf
            @method('PUT')

            @if($scoresByTeacher->count() > 0)
                <h5 class="fw-bold text-dark mb-3">📝 Edit Nilai yang Sudah Masuk</h5>
                
                @foreach($scoresByTeacher as $teacherId => $teacherScores)
                    @php
                        $teacherName = $teacherScores->first()->teacher ? $teacherScores->first()->teacher->name : 'Sistem / Admin';
                        $scoreMap = $teacherScores->pluck('score', 'criterion_id')->toArray();
                        $formKey = $teacherId ?: 'none'; 
                    @endphp
                    
                    <div class="card saas-card border-0 shadow-sm mb-4">
                        <div class="card-header bg-light border-bottom-0 pt-3 pb-2">
                            <h6 class="mb-0 fw-bold text-primary">👨‍🏫 Penilai: {{ $teacherName }}</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row">
                                @foreach($criteria as $criterion)
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold text-dark mb-1">
                                            {{ $criterion->name }}
                                            <span class="badge bg-light text-secondary border ms-1">{{ $criterion->code }}</span>
                                        </label>
                                        <select name="scores[{{ $formKey }}][{{ $criterion->id }}]" class="form-select bg-light shadow-sm" required>
                                            <option value="">-- Pilih Penilaian --</option>
                                            @foreach($criterion->scales as $scale)
                                                <option value="{{ $scale->scale_value }}" {{ (isset($scoreMap[$criterion->id]) && $scoreMap[$criterion->id] == $scale->scale_value) ? 'selected' : '' }}>
                                                    {{ $scale->scale_value }} - {{ $scale->description }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif

            <hr class="my-4">
            <h5 class="fw-bold text-success mb-3">➕ Input Penilaian Baru (Delegasi)</h5>
            <div class="card saas-card border-0 shadow-sm border-start border-success border-4 mb-4">
                <div class="card-body p-4">
                    <p class="text-muted small mb-4"><i>Catatan: Kosongkan bagian ini jika Anda hanya ingin mengedit nilai di atas. Jika ingin menambah nilai baru, pilih nama guru dan isi nilainya.</i></p>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark">Bertindak Atas Nama (Pilih Guru):</label>
                        <select name="selected_teacher_id" class="form-select border-success">
                            <option value="">-- Gunakan Akun Admin (Saya Sendiri) --</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}">👨‍🏫 {{ $teacher->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row">
                        @foreach($criteria as $criterion)
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">{{ $criterion->code }} - {{ $criterion->name }}</label>
                                <select name="new_admin_scores[{{ $criterion->id }}]" class="form-select border-success border-opacity-25">
                                    <option value="">-- Kosongkan Jika Tidak Perlu --</option>
                                    @foreach($criterion->scales as $scale)
                                        <option value="{{ $scale->scale_value }}">
                                            {{ $scale->scale_value }} - {{ $scale->description }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end mb-5">
                <button type="submit" class="btn btn-primary px-5 py-3 fw-bold shadow-sm" style="background-color: #4f46e5; border: none;">
                    💾 Simpan Semua Perubahan
                </button>
            </div>

        </form> </div>

    <div class="col-md-4">
        <div class="card saas-card bg-primary bg-opacity-10 border-0 position-sticky" style="top: 20px;">
            <div class="card-body p-4 text-primary">
                <h6 class="fw-bold mb-3">💡 Panduan Penilaian Veto</h6>
                <ul class="ps-3 mb-0" style="font-size: 0.9rem;">
                    <li class="mb-2"><strong>Edit:</strong> Anda bisa mengubah nilai yang sudah diinput oleh guru manapun di blok atas.</li>
                    <li class="mb-2"><strong>Delegasi:</strong> Anda bisa menginputkan nilai atas nama guru lain yang sedang berhalangan.</li>
                    <li>Perubahan akan langsung tercermin di layar masing-masing guru yang bersangkutan.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection