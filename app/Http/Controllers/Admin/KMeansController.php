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
use App\Models\Criterion;
use App\Models\User;

class KMeansController extends Controller
{
    // 1. Menampilkan Halaman Utama K-Means
    // 1. Menampilkan Halaman Utama K-Means (Dengan Filter)
    public function index(Request $request)
    {
        $query = CalculationLog::query();

        // Filter Berdasarkan Tanggal Ditetapkan
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // Filter Berdasarkan Jumlah Klaster (K)
        if ($request->filled('k_value')) {
            $query->where('k_value', $request->k_value);
        }

        // Ambil riwayat perhitungan dengan pagination (10 per halaman)
        $logs = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('admin.kmeans.index', compact('logs'));
    }

    // 2. Fungsi Bantuan: Menarik data, agregasi Multi-Rater, dan Filter Ketat
    private function getFormattedDataset()
    {
        // Kunci Pertahanan: Hitung ada berapa kriteria yang wajib diisi
        $totalCriteria = Criterion::count();

        $students = Student::with(['scores.criterion'])
            ->where('is_active', true)
            ->whereHas('scores')
            ->get();

        $dataset = [];
        foreach ($students as $student) {
            $tempScores = [];

            // Kumpulkan nilai dari banyak guru
            foreach ($student->scores as $score) {
                if ($score->criterion) {
                    $code = $score->criterion->code;
                    if (!isset($tempScores[$code])) {
                        $tempScores[$code] = [];
                    }
                    $tempScores[$code][] = $score->score;
                }
            }

            // FILTER KETAT: Hanya proses siswa yang SEMUA kriterianya sudah memiliki nilai minimal 1
            if (count($tempScores) === $totalCriteria && $totalCriteria > 0) {
                $dataset[$student->id] = [];

                // Hitung rata-rata (Agregasi Multi-Rater)
                foreach ($tempScores as $code => $scoresArray) {
                    $dataset[$student->id][$code] = array_sum($scoresArray) / count($scoresArray);
                }
            }
        }

        return $dataset;
    }

    // 3. API Eksekusi Elbow Method (Untuk Grafik AJAX)
    public function runElbow()
    {
        $dataset = $this->getFormattedDataset();

        if (count($dataset) < 3) {
            return response()->json(['error' => 'Data siswa (dengan nilai lengkap) minimal harus 3 orang untuk menjalankan Elbow.'], 400);
        }

        $elbowService = new ElbowService();
        $results = $elbowService->analyze($dataset, 10);

        return response()->json($results);
    }

    // 4. Proses Inti: Eksekusi K-Means via Python, Validasi DBI, dan SIMPAN
    public function calculate(Request $request)
    {
        $request->validate([
            'k_value' => 'required|integer|min:2|max:10'
        ]);

        $k = $request->k_value;

        // 1. Ambil data siswa
        $dataset = $this->getFormattedDataset();

        if (count($dataset) < $k) {
            return redirect()->back()->with('error', 'Jumlah siswa kurang dari nilai K.');
        }

        DB::beginTransaction();
        try {
            // 2. Panggil Mesin Python!
            $kmeansService = new \App\Services\KMeansService($k, $dataset);
            $pythonResult = $kmeansService->runPythonEngine();

            // 3. Ekstrak hasil dari Python
            $clusters = $pythonResult['clusters'];
            $centroids = $pythonResult['centroids'];
            $dbiScore = $pythonResult['dbi']; // DBI langsung dapet dari Python!
            $iterations = $pythonResult['iterations'];

            // 4. Simpan Log Induk
            $log = CalculationLog::create([
                'user_id' => auth()->id(),
                'k_value' => $k,
                'dbi_score' => $dbiScore,
                'total_iterations' => $iterations,
                'description' => 'Perhitungan K-Means dengan K=' . $k . ' (Python Engine)',
            ]);

            $studentsData = Student::whereIn('id', array_keys($dataset))->get()->keyBy('id');

            // 5. Simpan Hasil Detail Siswa
            // KUNCI PERBAIKAN: Gunakan $clusters, BUKAN $clusterResult['final_clusters']
            foreach ($clusters as $clusterIndex => $studentIds) {
                // Di Python, index mungkin dimulai dari "0", kita +1 agar di web jadi Klaster 1, 2, dst.
                $clusterNumber = intval($clusterIndex) + 1;

                foreach ($studentIds as $studentId) {
                    $studentInfo = $studentsData[$studentId];

                    CalculationResult::create([
                        'calculation_log_id' => $log->id,
                        'cluster_number' => $clusterNumber,
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
            return redirect()->route('admin.kmeans.result', $log->id)->with('success', 'Perhitungan K-Means berhasil dieksekusi dan disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['Terjadi kesalahan sistem: ' . $e->getMessage()]);
        }
    }

    // 5. Menampilkan Halaman Hasil Detail
    public function showResult($log_id)
    {
        $log = CalculationLog::with('results')->findOrFail($log_id);
        $history = [];
        return view('admin.kmeans.result', compact('log', 'history'));
    }
}
