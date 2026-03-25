@extends('layouts.master')

@section('title', 'Eksekusi K-Means & Metode Elbow')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h4 class="fw-bold mb-1">Eksekusi Algoritma K-Means</h4>
        <p class="text-muted">Gunakan Metode Elbow untuk mencari nilai K optimal, lalu jalankan klastering.</p>
    </div>
</div>

@if ($errors->any())
<div class="alert alert-danger border-0 shadow-sm">
    <ul class="mb-0">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="row">
    <div class="col-md-8 mb-4">
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
    </div>

    <div class="col-md-4 mb-4">
        <div class="card saas-card bg-primary bg-opacity-10 border-0 h-100">
            <div class="card-body p-4">
                <h6 class="fw-bold text-primary mb-3">⚙️ Jalankan Klastering</h6>
                <p class="text-muted small mb-4">Setelah melihat grafik Elbow, masukkan nilai K yang Anda inginkan di bawah ini untuk memulai pengelompokan final.</p>

                <form action="{{ route('admin.kmeans.calculate') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Jumlah Klaster (K)</label>
                        <input type="number" name="k_value" class="form-control form-control-lg border-primary shadow-sm" placeholder="Contoh: 3" min="2" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow" style="background-color: #4f46e5; border: none;">
                        Mulai Eksekusi K-Means 🚀
                    </button>
                </form>
            </div>
        </div>
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
                        <td class="fw-bold text-primary">{{ $log->k_value }} Klaster</td>
                        <td>
                            @if($log->dbi_score < 0.5)
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">{{ number_format($log->dbi_score, 4) }} (Sangat Baik)</span>
                            @elseif($log->dbi_score < 1.0)
                                <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3">{{ number_format($log->dbi_score, 4) }} (Cukup)</span>
                            @else
                                <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3">{{ number_format($log->dbi_score, 4) }} (Buruk)</span>
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
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
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
</script>
@endsection
