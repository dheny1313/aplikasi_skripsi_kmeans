<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StudentImport;


class StudentController extends Controller
{
    // Menampilkan halaman daftar siswa beserta fitur filter
    public function index(Request $request)
    {
        // 1. Inisiasi Query Builder ke tabel Student
        $query = Student::query();

        // 2. Filter Pencarian Teks (Mencari berdasarkan student_id ATAU name)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('student_id', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            });
        }

        // 3. Filter Dropdown Jenis Kelamin
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        // 4. Filter Dropdown Status (Aktif / Non-aktif)
        // Kita gunakan string '1' untuk aktif, dan '0' untuk non-aktif dari form HTML nanti
        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        // 5. Eksekusi query dan ambil datanya dn pakai pagination
        $students = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();


        return view('admin.student.index', compact('students'));
    }

    //store
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|unique:student,student_id',
            'name' => 'required|string|max:255',
            'gender' => 'required|in:L,P',
        ]);

        Student::create([
            'student_id' => $request->student_id,
            'name' => $request->name,
            'gender' => $request->gender,
            'is_active' => true, // Default aktif saat baru dibuat
        ]);

        return redirect()->back()->with('success', 'Data Siswa Berhasil Ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $request->validate([
            // Validasi unique kecuali untuk ID siswa ini sendiri
            'student_id' => 'required|unique:student,student_id,' . $student->id,
            'name' => 'required|string|max:255',
            'gender' => 'required|in:L,P',
        ]);

        $student->update([
            'student_id' => $request->student_id,
            'name' => $request->name,
            'gender' => $request->gender,
        ]);

        return redirect()->back()->with('success', 'Data siswa berhasil diperbarui!');
    }

    // Mengubah status Aktif / Non-aktif (Soft Disable)
    public function toggleStatus($id)
    {
        $student = Student::findOrFail($id);
        $student->is_active = !$student->is_active; // Balikkan status true/false
        $student->save();

        $status = $student->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->back()->with('success', "Status siswa berhasil $status!");
    }


    //import data siswa
    // Memproses upload Excel
    public function import(Request $request)
    {
        // Validasi file yang diupload wajib format excel/csv
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:2048'
        ], [
            'file_excel.required' => 'Pilih file Excel terlebih dahulu.',
            'file_excel.mimes' => 'Format file wajib berupa .xlsx, .xls, atau .csv.'
        ]);

        try {
            Excel::import(new StudentImport, $request->file('file_excel'));
            return redirect()->back()->with('success', 'Data siswa massal berhasil diimpor!');
        } catch (\Exception $e) {
            // Menangkap error jika format excel salah atau ada masalah sistem
            return redirect()->back()->withErrors(['Terjadi kesalahan saat impor data: ' . $e->getMessage()]);
        }
    }
}
