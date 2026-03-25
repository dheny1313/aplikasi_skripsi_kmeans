<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Criterion;

class CriterionController extends Controller
{
    public function index(Request $request)
    {
        // 1. Tambahkan with('scales') agar data deskripsi skala ikut terpanggil
        $query = Criterion::with('scales');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        $criteria = $query->orderBy('code', 'asc')->paginate(10)->withQueryString();
        
        return view('admin.criteria.index', compact('criteria'));
    }

    public function store(Request $request)
    {
        // 2. Tambahkan validasi untuk input skala 1 sampai 5
        $request->validate([
            'code' => 'required|unique:criteria,code',
            'name' => 'required|string|max:255',
            'weight' => 'required|numeric|min:0',
            'scale_1' => 'required|string|max:255',
            'scale_2' => 'required|string|max:255',
            'scale_3' => 'required|string|max:255',
            'scale_4' => 'required|string|max:255',
            'scale_5' => 'required|string|max:255',
        ]);

        // 3. Simpan data induk kriteria
        $criterion = Criterion::create([
            'code' => $request->code,
            'name' => $request->name,
            'weight' => $request->weight,
        ]);

        // 4. Looping untuk menyimpan kelima skala ke tabel criterion_scales
        for ($i = 1; $i <= 5; $i++) {
            $criterion->scales()->create([
                'scale_value' => $i,
                'description' => $request->input('scale_' . $i)
            ]);
        }

        return redirect()->back()->with('success', 'Data kriteria dan rubrik penilaian berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $criterion = Criterion::findOrFail($id);
        
        $request->validate([
            'code' => 'required|unique:criteria,code,' . $criterion->id,
            'name' => 'required|string|max:255',
            'weight' => 'required|numeric|min:0',
            'scale_1' => 'required|string|max:255',
            'scale_2' => 'required|string|max:255',
            'scale_3' => 'required|string|max:255',
            'scale_4' => 'required|string|max:255',
            'scale_5' => 'required|string|max:255',
        ]);

        // Update data induk kriteria
        $criterion->update([
            'code' => $request->code,
            'name' => $request->name,
            'weight' => $request->weight,
        ]);

        // Update atau buat deskripsi skalanya
        for ($i = 1; $i <= 5; $i++) {
            $criterion->scales()->updateOrCreate(
                ['scale_value' => $i], // Cari berdasarkan nilai skalanya (1,2,3,4,5)
                ['description' => $request->input('scale_' . $i)] // Update deskripsinya
            );
        }

        return redirect()->back()->with('success', 'Data kriteria dan rubrik penilaian berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $criterion = Criterion::findOrFail($id);
        $criterion->delete(); // Data di tabel scales otomatis terhapus karena aturan 'cascade' di migrasi

        return redirect()->back()->with('success', 'Data kriteria berhasil dihapus!');
    }
}
