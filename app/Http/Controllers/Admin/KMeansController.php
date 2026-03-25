<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CalculationLog;
use App\Models\CalculationResult;
use App\Models\Student;
use App\Services\KMeansService;
use App\Services\DBIService;
use App\Services\ElbowService;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class KMeansController extends Controller
{
    //
    // 1. Menampilkan Halaman Utama K-Means (Form Input K & Tombol Elbow)
    public function index()
    {
        // Ambil riwayat perhitungan sebelumnya (jika ada)
        $logs = CalculationLog::orderBy('created_at', 'desc')->get();
        return view('admin.kmeans.index', compact('logs'));
    }

    // 2. Fungsi Bantuan: Menarik data siswa & nilai dari Database lalu diubah ke Array
    private function getFormattedDataset()
    {
        $students = Student::with(['scores.criterion'])
            ->where('is_active', true)
            ->whereHas('scores')
            ->get();

        $dataset = [];
        foreach ($students as $student) {
            $dataset[$student->id] = [];

            // Kita kumpulkan semua nilai dari berbagai guru ke dalam satu array sementara
            $tempScores = [];
            foreach ($student->scores as $score) {
                $code = $score->criterion->code;
                if (!isset($tempScores[$code])) {
                    $tempScores[$code] = [];
                }
                $tempScores[$code][] = $score->score; // Masukkan nilai dari guru A, B, dst.
            }

            // Hitung rata-ratanya untuk masing-masing kriteria
            foreach ($tempScores as $code => $scoresArray) {
                // array_sum / count = Rumus Rata-rata (Mean)
                $dataset[$student->id][$code] = array_sum($scoresArray) / count($scoresArray);
            }
        }

        return $dataset;
    }

    // 3. API untuk mengeksekusi Elbow Method (Dipanggil via AJAX untuk Grafik)
    public function runElbow()
    {
        $dataset = $this->getFormattedDataset();

        if (count($dataset) < 3) {
            return response()->json(['error' => 'Data siswa yang sudah dinilai minimal harus 3 orang untuk menjalankan Elbow.'], 400);
        }

        $elbowService = new ElbowService();
        $results = $elbowService->analyze($dataset, 10); // Maksimal simulasi K=10

        return response()->json($results);
    }

    // 4. Proses Inti: Eksekusi K-Means, Validasi DBI, dan SIMPAN ke Database
    public function calculate(Request $request)
    {
        $request->validate([
            'k_value' => 'required|integer|min:2' // K minimal 2
        ]);

        $k = $request->k_value;
        $dataset = $this->getFormattedDataset();

        if (count($dataset) < $k) {
            return redirect()->back()->withErrors(['Jumlah siswa yang sudah dinilai lebih sedikit dari jumlah Klaster (K). Input lebih banyak nilai siswa terlebih dahulu.']);
        }

        // --- MULAI PROSES ALGORITMA ---

        // A. Panggil Mesin K-Means
        $kmeans = new KMeansService($k, $dataset);
        $clusterResult = $kmeans->cluster();

        // B. Panggil Mesin Validasi DBI
        $dbi = new DBIService();
        $dbiScore = $dbi->evaluate($dataset, $clusterResult['final_clusters'], $clusterResult['final_centroids']);

        // --- PROSES SIMPAN KE DATABASE (Aman dengan Transaction) ---
        // --- PROSES SIMPAN KE DATABASE (Aman dengan Transaction) ---
        DB::beginTransaction();
        try {
            // 1. Simpan Log Induk
            // 1. Simpan Log Induk (Sesuai dengan Model Anda)
            $log = CalculationLog::create([
                'user_id' => auth()->id(), // Menyimpan ID Admin yang mengeksekusi
                'k_value' => $k,
                'dbi_score' => $dbiScore,
                'total_iterations' => $clusterResult['total_iterations'],
                'description' => 'Perhitungan K-Means dengan K=' . $k,
            ]);

            // Ambil detail nama & NIS siswa untuk dibekukan ke dalam snapshot
            $studentsData = Student::whereIn('id', array_keys($dataset))->get()->keyBy('id');

            // 2. Simpan Hasil Detail Siswanya (Berdasarkan Model Anda)
            foreach ($clusterResult['final_clusters'] as $clusterIndex => $studentIds) {
                $clusterNumber = $clusterIndex + 1;

                foreach ($studentIds as $studentId) {
                    $studentInfo = $studentsData[$studentId];

                    CalculationResult::create([
                        'calculation_log_id' => $log->id,
                        'cluster_number' => $clusterNumber,
                        // Simpan data mentah siswa saat ini sebagai riwayat abadi
                        'snapshot_data' => [
                            'student_id' => $studentInfo->id,
                            'nis' => $studentInfo->student_id,
                            'name' => $studentInfo->name,
                            'scores' => $dataset[$studentId]
                        ]
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.kmeans.result', $log->id)->with('success', 'Perhitungan K-Means berhasil dan data telah disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['Terjadi kesalahan saat menyimpan ke database: ' . $e->getMessage()]);
        }
    }

    // 5. Menampilkan Halaman Hasil Detail (Iterasi & Anggota Klaster)
    // 5. Menampilkan Halaman Hasil Detail
    public function showResult($log_id)
    {
        // Hapus 'results.student', cukup 'results' saja
        $log = CalculationLog::with('results')->findOrFail($log_id);

        //$history = json_decode($log->history_snapshot, true) ?? [];
        // Kita set array kosong karena tabel Anda tidak menyimpan riwayat iterasi
        $history = [];

        return view('admin.kmeans.result', compact('log', 'history'));
    }
}
