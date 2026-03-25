<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudentScore;
use App\Models\Student;
use App\Models\Criterion;


class ScoreController extends Controller
{
    // 1. Menampilkan daftar siswa untuk dinilai
   public function index(Request $request)
    {
        $totalCriteria = Criterion::count();
        $query = Student::where('is_active', true);
        $teacherId = auth()->id();

        // 1. Filter Pencarian Nama/NIS
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('student_id', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            });
        }

        // 2. Filter Status Penilaian (Khusus untuk Guru yang sedang login)
        if ($request->filled('status')) {
            if ($request->status == 'belum') {
                $query->whereDoesntHave('scores', function($q) use ($teacherId) {
                    $q->where('teacher_id', $teacherId);
                });
            } elseif ($request->status == 'sudah') {
                $query->whereHas('scores', function($q) use ($teacherId) {
                    $q->where('teacher_id', $teacherId);
                });
            }
        }

        // Relasi scores tetap dilimit untuk badge
        $students = $query->with(['scores' => function($q) use ($teacherId) {
            $q->where('teacher_id', $teacherId);
        }])->orderBy('name', 'asc')->paginate(15)->withQueryString();

        return view('teacher.score.index', compact('students', 'totalCriteria'));
    }

    // 2. Menampilkan Form Input Nilai Dinamis untuk 1 Siswa
    public function edit($student_id)
    {
        $student = Student::findOrFail($student_id);
        $criteria = Criterion::with('scales')->orderBy('code', 'asc')->get();

        // Hanya ambil nilai yang diinput oleh guru ini sendiri (auth()->id())
        $existingScores = StudentScore::where('student_id', $student->id)
            ->where('teacher_id', auth()->id())
            ->pluck('score', 'criterion_id')
            ->toArray();

        return view('teacher.score.form', compact('student', 'criteria', 'existingScores'));
    }

    // 3. Menyimpan Nilai Dinamis ke Database
    public function update(Request $request, $student_id)
    {
        $student = Student::findOrFail($student_id);
        $criteria = Criterion::all();

        // Validasi: Pastikan setiap kriteria diisi dengan angka
        $rules = [];
        foreach ($criteria as $criterion) {
            $rules['score_' . $criterion->id] = 'required|numeric|min:0|max:100';
        }
        $request->validate($rules, [
            'required' => 'Semua kolom nilai wajib diisi.',
            'numeric' => 'Nilai harus berupa angka.',
            'max' => 'Nilai maksimal adalah 100.'
        ]);

        // Looping untuk menyimpan/update nilai per kriteria ke tabel student_score
        foreach ($criteria as $criterion) {
            StudentScore::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'criterion_id' => $criterion->id,
                    'teacher_id' => auth()->id() // KUNCI UTAMANYA DI SINI
                ],
                [
                    'score' => $request->input('score_' . $criterion->id)
                ]
            );
        }

        return redirect()->route('teacher.score.index')->with('success', 'Nilai berhasil disimpan!');
    }
}
