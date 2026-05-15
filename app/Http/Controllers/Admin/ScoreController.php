<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\SCoreImport;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Criterion;
use App\Models\StudentScore;
use Maatwebsite\Excel\Facades\Excel;

class ScoreController extends Controller
{
    //
    public function index(Request $request)
    {
        $totalCriteria = Criterion::count();
        $query = Student::where('is_active', true);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('student_id', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status == 'belum') {
                $query->doesntHave('scores');
            } elseif ($request->status == 'sudah') {
                $query->has('scores');
            }
        }

        $students = $query->with('scores')->orderBy('name', 'asc')->paginate(15)->withQueryString();

        // TAMBAHKAN INI: Ambil data guru aktif untuk dropdown di Modal Import Excel
        $teachers = \App\Models\User::where('role', 'teacher')->where('is_active', true)->orderBy('name', 'asc')->get();

        // Jangan lupa tambahkan $teachers ke compact()
        return view('admin.score.index', compact('students', 'totalCriteria', 'teachers'));
    }

    // Sesuaikan method import untuk menangkap dan mengoper teacher_id
    public function import(Request $request)
    {
        // Validasi disesuaikan dengan nama input di form (file_excel)
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv',
            'teacher_id' => 'nullable|exists:users,id'
        ]);

        try {
            // Panggil file-nya menggunakan nama 'file_excel'
            \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\SCoreImport($request->teacher_id), $request->file('file_excel'));

            return redirect()->back()->with('success', 'Data nilai berhasil diimpor!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['Terjadi kesalahan saat import: ' . $e->getMessage()]);
        }
    }

    // Menampilkan Detail Siapa Saja Guru yang Menilai Siswa Ini
    public function show($student_id)
    {
        $student = Student::findOrFail($student_id);
        $criteria = Criterion::orderBy('code', 'asc')->get();

        // Ambil semua nilai milik siswa ini beserta relasi ke tabel users (guru)
        $scores = StudentScore::with(['teacher', 'criterion'])
            ->where('student_id', $student->id)
            ->get();

        // Kelompokkan data nilai berdasarkan ID Guru (teacher_id)
        $scoresByTeacher = $scores->groupBy('teacher_id');

        return view('admin.score.show', compact('student', 'criteria', 'scoresByTeacher'));
    }

    // 2. Halaman Edit Super-Form Admin (Bisa edit dan delegasi input)
    public function edit($student_id)
    {
        $student = Student::findOrFail($student_id);
        $criteria = Criterion::with('scales')->orderBy('code', 'asc')->get();

        $scores = StudentScore::with('teacher')
            ->where('student_id', $student->id)
            ->get();

        $scoresByTeacher = $scores->groupBy('teacher_id');

        // Panggil semua daftar akun pengguna (Guru) selain Admin yang sedang login
        $teachers = \App\Models\User::where('id', '!=', auth()->id())
            ->where('is_active', true) // TAMBAHKAN BARIS INI
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.score.form', compact('student', 'criteria', 'scoresByTeacher', 'teachers'));
    }

    // 3. Simpan Perubahan Nilai (Update Massal & Delegasi Input Baru)
    public function update(Request $request, $student_id)
    {
        $student = Student::findOrFail($student_id);

        $request->validate([
            'scores' => 'nullable|array',
            'scores.*.*' => 'required|integer|min:1|max:5',
            'new_admin_scores' => 'nullable|array',
            'new_admin_scores.*' => 'nullable|integer|min:1|max:5',
            'selected_teacher_id' => 'nullable|exists:users,id' // Validasi ID Guru
        ]);

        // A. Update Nilai Milik Guru yang Sudah Ada (Biarkan Sama)
        if ($request->has('scores')) {
            foreach ($request->scores as $teacherId => $criteriaScores) {
                $actualTeacherId = ($teacherId === 'none') ? null : $teacherId;

                foreach ($criteriaScores as $criterionId => $scoreValue) {
                    StudentScore::updateOrCreate(
                        [
                            'student_id' => $student->id,
                            'criterion_id' => $criterionId,
                            'teacher_id' => $actualTeacherId
                        ],
                        ['score' => $scoreValue]
                    );
                }
            }
        }

        // B. Jika Admin menginput nilai baru (Bisa Atas Nama Sendiri atau Atas Nama Guru Lain)
        if ($request->has('new_admin_scores')) {
            // Cek apakah Admin memilih guru dari dropdown. Jika tidak, pakai ID Admin sendiri.
            $assignedTeacherId = $request->selected_teacher_id ?: auth()->id();

            foreach ($request->new_admin_scores as $criterionId => $scoreValue) {
                if (!empty($scoreValue)) {
                    StudentScore::updateOrCreate(
                        [
                            'student_id' => $student->id,
                            'criterion_id' => $criterionId,
                            'teacher_id' => $assignedTeacherId // Disimpan atas nama guru yang dipilih
                        ],
                        ['score' => $scoreValue]
                    );
                }
            }
        }

        return redirect()->route('admin.score.show', $student->id)->with('success', 'Data nilai berhasil diperbarui dan didelegasikan!');
    }
}
