<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CalculationLog;


class KMeansController extends Controller
{
    // 1. Menampilkan Daftar Riwayat Perhitungan dengan Filter
    public function index(Request $request)
    {
        $query = CalculationLog::query();

        // Filter A: Berdasarkan Tanggal Ditetapkan
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // Filter B: Berdasarkan Jumlah Klaster (K)
        if ($request->filled('k_value')) {
            $query->where('k_value', $request->k_value);
        }

        // Gunakan paginate agar data rapi saat sudah banyak
        $logs = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('teacher.kmeans.index', compact('logs'));
    }

    // 2. Menampilkan Detail Anggota Klaster
    public function showResult($log_id)
    {
        $log = CalculationLog::with('results')->findOrFail($log_id);
        return view('teacher.kmeans.result', compact('log'));
    }
}
