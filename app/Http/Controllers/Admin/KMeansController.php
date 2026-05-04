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

    private function runNormalizationLogic()
    {
        $totalCriteria = \App\Models\Criterion::count();
        if ($totalCriteria === 0) {
            return ['status' => false, 'message' => 'Data kriteria belum diatur. Silakan tambahkan kriteria terlebih dahulu.'];
        }

        $activeTeacherCount = \App\Models\User::where('role', 'teacher')->where('is_active', true)->count();
        if ($activeTeacherCount === 0) {
            return ['status' => false, 'message' => 'Tidak ada guru aktif yang dapat menilai.'];
        }

        $students = \App\Models\Student::with(['scores' => function($query) {
                $query->whereHas('teacher', function($q) {
                    $q->where('is_active', true);
                })->with('criterion');
            }])
            ->where('is_active', true)
            ->whereHas('scores', function($query) {
                $query->whereHas('teacher', function($q) {
                    $q->where('is_active', true);
                });
            })
            ->get();

        if ($students->isEmpty()) {
            return ['status' => false, 'message' => 'Tidak ada data siswa aktif yang memiliki nilai (dari guru aktif) untuk diproses.'];
        }

        $aggregatedData = [];
        $incompleteStudents = [];

        foreach ($students as $student) {
            $tempScores = [];
            foreach ($student->scores as $score) {
                if ($score->criterion) {
                    $code = $score->criterion->code;
                    $tempScores[$code][] = $score->score;
                }
            }

            if (count($tempScores) === $totalCriteria) {
                $isComplete = true;
                foreach ($tempScores as $code => $scoresArray) {
                    if (count($scoresArray) < $activeTeacherCount) {
                        $isComplete = false;
                        break;
                    }
                }

                if ($isComplete) {
                    $aggregatedData[$student->id] = [];
                    foreach ($tempScores as $code => $scoresArray) {
                        $avg = array_sum($scoresArray) / count($scoresArray);
                        $aggregatedData[$student->id][$code] = round($avg, 2);
                    }
                } else {
                    $incompleteStudents[] = $student->name . " (Hanya dinilai sebagian guru)";
                }
            } else {
                $incompleteStudents[] = $student->name . " (Kriteria tidak lengkap)";
            }
        }

        if (count($incompleteStudents) > 0) {
            $sampleNames = implode(', ', array_slice($incompleteStudents, 0, 3));
            $moreText = count($incompleteStudents) > 3 ? ' dan ' . (count($incompleteStudents) - 3) . ' lainnya' : '';
            return ['status' => false, 'message' => "Terdapat " . count($incompleteStudents) . " siswa yang belum lengkap dinilai oleh seluruh $activeTeacherCount guru aktif ($sampleNames$moreText)."];
        }

        if (count($aggregatedData) < 3) {
            return ['status' => false, 'message' => 'Data valid terlalu sedikit. Minimal butuh 3 siswa dengan nilai lengkap dari semua guru aktif untuk melakukan clustering.'];
        }

        DB::beginTransaction();
        try {
            \App\Models\NormalizedScore::query()->delete();
            foreach ($aggregatedData as $studentId => $scores) {
                \App\Models\NormalizedScore::create([
                    'student_id' => $studentId,
                    'normalized_data' => $scores
                ]);
            }
            DB::commit();
            return ['status' => true, 'message' => 'Data siswa berhasil diagregasi dan disinkronisasi ke database.'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Gagal menyimpan data agregasi: ' . $e->getMessage()];
        }
    }

    public function syncNormalization()
    {
        $result = $this->runNormalizationLogic();
        
        if ($result['status']) {
            return redirect()->back()->with('success', $result['message']);
        } else {
            return redirect()->back()->with('error', 'Validasi Gagal: ' . $result['message']);
        }
    }

    private function getFormattedDataset()
    {
        // Langsung tarik data matang dari tabel, diurutkan ID agar sekuensial
        $normalizedRecords = \App\Models\NormalizedScore::orderBy('student_id', 'asc')->get();

        $dataset = [];
        foreach ($normalizedRecords as $record) {
            $dataset[$record->student_id] = $record->normalized_data;
        }

        return $dataset;
    }

    // 3. API Eksekusi Elbow Method (Untuk Grafik AJAX)
    public function runElbow()
    {
        // 1. Validasi dan Sinkronisasi Data Otomatis Sebelum Elbow
        $syncResult = $this->runNormalizationLogic();
        
        if (!$syncResult['status']) {
            return response()->json(['error' => 'Data tidak siap untuk grafik Elbow: ' . $syncResult['message']], 400);
        }

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

        // ==========================================
        // VALIDASI & SINKRONISASI OTOMATIS DATA TERBARU
        // ==========================================
        $syncResult = $this->runNormalizationLogic();
        
        if (!$syncResult['status']) {
            return redirect()->route('admin.data.normalization')
                ->with('error', 'Gagal Klastering. Ada perubahan data master dan sistem tidak dapat menyinkronkan data baru karena: ' . $syncResult['message'] . ' Harap perbaiki data penilaian terlebih dahulu.');
        }
        // ==========================================

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

    //menampilkan data normalisasi
    // Menampilkan Halaman Menu Khusus Normalisasi
    public function normalizationIndex()
    {
        // Tarik data normalisasi beserta nama siswanya
        $normalizedData = \App\Models\NormalizedScore::with('student')
            ->orderBy('student_id', 'asc')
            ->get();

        return view('admin.kmeans.normalization', compact('normalizedData'));
    }

    // 5. Menampilkan Halaman Hasil Detail
    public function showResult($log_id)
    {
        $log = CalculationLog::with('results')->findOrFail($log_id);
        $history = [];
        return view('admin.kmeans.result', compact('log', 'history'));
    }
}
