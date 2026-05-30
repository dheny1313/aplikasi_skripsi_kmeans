@extends('layouts.master')

@section('title', 'Eksekusi K-Means ')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h4 class="fw-bold mb-1">Eksekusi Algoritma K-Means</h4>
        <p class="text-muted">Gunakan Metode Elbow untuk mencari nilai K optimal, lalu jalankan klastering.</p>
    </div>
</div>

@if(session('error'))
<div class="alert alert-danger border-0 shadow-sm alert-dismissible fade show" role="alert">
    <strong>Peringatan! Validasi Gagal:</strong> {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('success'))
<div class="alert alert-success border-0 shadow-sm alert-dismissible fade show" role="alert">
    <strong>Berhasil!</strong> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if ($errors->any())
<div class="alert alert-danger border-0 shadow-sm alert-dismissible fade show">
    <ul class="mb-0">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="row">
    {{-- <div class="col-md-8 mb-4">
        <div class="card saas-card h-100">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0">📈 Analisis Metode Elbow</h6>
                <button id="btnRunElbow" class="btn btn-sm btn-outline-primary fw-semibold rounded-pill px-3">
                    Jalankan Analisis
                </button>
            </div>
            <div class="card-body">
                <p class="text-muted" style="font-size: 0.85rem;">Klik tombol di atas untuk melihat grafik penurunan nilai WCSS. Titik di mana grafik membentuk "siku" (patahan tajam) adalah jumlah klaster (K) yang paling direkomendasikan.</p>

                <div id="elbowLoading" class="text-center py-5 d-none">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 text-muted small">Sedang memproses simulasi K=1 hingga 10...</p>
                </div>

                <canvas id="elbowChart" style="max-height: 300px;"></canvas>
            </div>
        </div>
    </div> --}}

    <div class="col-md-7 mb-4">
        <div class="card saas-card bg-primary bg-opacity-10 border-0 h-100 shadow-sm">
            <div class="card-body p-4">
                <h6 class="fw-bold text-primary mb-3">⚙️ Jalankan Klastering</h6>
                <p class="text-muted small mb-4">Masukkan nilai K (jumlah kelompok) yang Anda inginkan di bawah ini untuk memulai pengelompokan final berdasarkan data yang sudah dinormalisasi.</p>
                <form action="{{ route('admin.kmeans.calculate') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Jumlah Klaster (K)</label>
                        <input type="number" id="k_value" name="k_value" class="form-control form-control-lg border-primary shadow-sm" placeholder="Contoh: 3" min="2" max="10" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Metode Inisialisasi Centroid</label>
                        <select name="init_method" id="init_method" class="form-select border-primary shadow-sm">
                            <option value="sequential">Sekuensial (Urutan Pertama)</option>
                            <option value="random">Acak (Random)</option>
                            <option value="manual">Manual (Pilih Siswa)</option>
                        </select>
                    </div>

                    <div id="manual_centroids_container" class="mb-3 d-none p-3 bg-white rounded border border-primary shadow-sm">
                        <label class="form-label fw-bold text-primary mb-2"><i class="fas fa-hand-pointer me-1"></i> Pilih Siswa untuk Centroid Awal:</label>
                        <div id="manual_centroids_list">
                            <!-- Injected by JS -->
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow" style="background-color: #4f46e5; border: none;">
                        Mulai Eksekusi K-Means 🚀
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-5 mb-4">
        <div class="card saas-card border-0 h-100 shadow-sm">
            <div class="card-body p-4">
                <h6 class="fw-bold text-dark mb-3">📊 Deskripsi Data Saat Ini</h6>
                <p class="text-muted small mb-4">Ringkasan data master yang akan dikomputasi pada proses klastering kali ini:</p>
                
                <ul class="list-group list-group-flush border-top">
                    <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                        <span class="text-muted"><i class="fas fa-list me-2"></i>Kriteria Penilaian</span>
                        <span class="badge bg-secondary rounded-pill px-3 py-2">{{ $stats['criteria_count'] }} Kriteria</span>
                    </li>
                    <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                        <span class="text-muted"><i class="fas fa-user-tie me-2"></i>Guru Penilai (Aktif)</span>
                        <span class="badge bg-secondary rounded-pill px-3 py-2">{{ $stats['teacher_count'] }} Guru</span>
                    </li>
                    <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                        <span class="text-muted"><i class="fas fa-user-graduate me-2"></i>Siswa Siap Proses</span>
                        @if($stats['student_count'] >= 3)
                            <span class="badge bg-success rounded-pill px-3 py-2">{{ $stats['student_count'] }} Siswa</span>
                        @else
                            <span class="badge bg-danger rounded-pill px-3 py-2">{{ $stats['student_count'] }} Siswa (Kurang)</span>
                        @endif
                    </li>
                </ul>
                <div class="mt-3 text-center">
                    <small class="text-muted" style="font-size: 0.75rem;">*Sistem secara otomatis akan menormalisasi nilai dari guru aktif.</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- KOTAK FILTER RIWAYAT -->
<div class="card saas-card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form action="{{ route('admin.kmeans.index') }}" method="GET" class="mb-0">
            <div class="row g-2 align-items-center">

                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted" title="Cari berdasarkan tanggal">📅</span>
                        <input type="date" name="date" class="form-control border-start-0 bg-light" value="{{ request('date') }}">
                    </div>
                </div>

               <div class="col-md-4">
                    <select name="k_value" class="form-select border-light shadow-sm text-secondary">
                        <option value="">-- Semua Jumlah Klaster --</option>

                        @for ($i = 2; $i <= 10; $i++)
                            <option value="{{ $i }}" {{ request('k_value') == $i ? 'selected' : '' }}>
                                {{ $i }} Klaster
                            </option>
                        @endfor

                    </select>
                </div>

                <div class="col-md-3">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100 fw-semibold shadow-sm" style="background-color: #4f46e5; border: none;">Filter</button>

                        @if(request()->hasAny(['date', 'k_value']))
                            <a href="{{ route('admin.kmeans.index') }}" class="btn btn-light border text-danger shadow-sm" title="Reset Semua Filter">✖</a>
                        @endif
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>

<div class="card saas-card">
    <div class="card-header bg-white pt-4 pb-2 border-bottom-0">
        <h6 class="fw-bold mb-0">🕒 Riwayat Perhitungan Sebelumnya</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-center">
                <thead class="bg-light text-muted" style="font-size: 0.85rem; text-transform: uppercase;">
                    <tr>
                        <th class="py-3 text-start ps-4">Waktu Eksekusi</th>
                        <th class="py-3">Nilai K</th>
                        <th class="py-3">Skor DBI (Validasi)</th>
                        <th class="py-3 text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody style="font-size: 0.95rem;">
                    @forelse ($logs as $log)
                    <tr>
                        <td class="text-start ps-4">{{ $log->created_at->format('d M Y, H:i') }}</td>
                        <td class="fw-bold text-primary">
                            {{ $log->k_value }} Klaster
                            <div class="text-muted fw-normal mt-1" style="font-size: 0.75rem;">{{ $log->description }}</div>
                        </td>
                        <td>
                            @if($log->dbi_score < 0.5)
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">{{ number_format($log->dbi_score, 4) }} </span>
                            @elseif($log->dbi_score < 1.0)
                                <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3">{{ number_format($log->dbi_score, 4) }} </span>
                            @else
                                <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3">{{ number_format($log->dbi_score, 4) }} </span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('admin.kmeans.result', $log->id) }}" class="btn btn-sm btn-light border shadow-sm">Lihat Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-5 text-muted">Belum ada riwayat perhitungan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
           <!-- PAGINATION (Tombol Next/Prev) -->
                   {{-- pagination  --}}
        <div class="d-flex justify-content-between align-items-center mt-3 px-3 pb-2">
            <div class="text-muted" style="font-size: 0.85rem;">
                Menampilkan data perhitungan ke {{ $logs->firstItem() ?? 0 }} sampai {{ $logs->lastItem() ?? 0 }} dari total {{ $logs->total() }} pethitungan K-Means.
            </div>
            <div>
                {{ $logs->links() }}
            </div>
        </div>

    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const kInput = document.getElementById('k_value');
        const initSelect = document.getElementById('init_method');
        const manualContainer = document.getElementById('manual_centroids_container');
        const manualList = document.getElementById('manual_centroids_list');

        const students = @json($readyStudents->map(function($s) { return ['id' => $s->student->id, 'name' => $s->student->name, 'nis' => $s->student->student_id]; })->values());

        function updateManualUI() {
            if (initSelect.value === 'manual') {
                manualContainer.classList.remove('d-none');
                let k = parseInt(kInput.value) || 0;
                
                if(k > 0) {
                    if(k > 10) k = 10;
                    
                    let html = '';
                    for(let i = 1; i <= k; i++) {
                        html += `<div class="mb-2">
                            <label class="form-label small text-muted mb-1 fw-bold">Centroid ${i}</label>
                            <select name="manual_centroids[]" class="form-select form-select-sm border-secondary shadow-sm" required>
                                <option value="">-- Pilih Siswa --</option>`;
                        students.forEach(student => {
                            html += `<option value="${student.id}">${student.nis} - ${student.name}</option>`;
                        });
                        html += `</select></div>`;
                    }
                    manualList.innerHTML = html;
                } else {
                    manualList.innerHTML = '<div class="alert alert-warning py-2 mb-0 small">Silakan isi Jumlah Klaster (K) terlebih dahulu.</div>';
                }
            } else {
                manualContainer.classList.add('d-none');
                manualList.innerHTML = '';
            }
        }

        initSelect.addEventListener('change', updateManualUI);
        kInput.addEventListener('input', updateManualUI);
    });
</script>

{{-- metode elbow --}}
{{-- <script>
    document.getElementById('btnRunElbow').addEventListener('click', function() {
        // Tampilkan loading, sembunyikan canvas
        document.getElementById('elbowLoading').classList.remove('d-none');
        document.getElementById('elbowChart').style.display = 'none';
        this.disabled = true;

        // Panggil API Elbow via AJAX Fetch
        fetch("{{ route('admin.kmeans.elbow') }}")
            .then(response => response.json())
            .then(data => {
                if(data.error) {
                    alert(data.error);
                    location.reload();
                    return;
                }

                // Siapkan data untuk Chart.js
                const labels = data.map(item => 'K = ' + item.k);
                const wcssData = data.map(item => item.wcss);

                // Render Chart
                document.getElementById('elbowLoading').classList.add('d-none');
                document.getElementById('elbowChart').style.display = 'block';

                const ctx = document.getElementById('elbowChart').getContext('2d');

                // Hapus chart lama jika ada agar tidak menumpuk
                if(window.elbowLineChart) {
                    window.elbowLineChart.destroy();
                }

                window.elbowLineChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Nilai WCSS',
                            data: wcssData,
                            borderColor: '#4f46e5',
                            backgroundColor: 'rgba(79, 70, 229, 0.1)',
                            borderWidth: 3,
                            pointBackgroundColor: '#fff',
                            pointBorderColor: '#4f46e5',
                            pointRadius: 5,
                            pointHoverRadius: 7,
                            fill: true,
                            tension: 0.1 // Sedikit melengkung
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: { beginAtZero: false, title: { display: true, text: 'WCSS (Variansi)' } },
                            x: { title: { display: true, text: 'Jumlah Klaster (K)' } }
                        }
                    }
                });

                document.getElementById('btnRunElbow').disabled = false;
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat mengambil data Elbow.');
                location.reload();
            });
    });
</script> --}}
@endsection
