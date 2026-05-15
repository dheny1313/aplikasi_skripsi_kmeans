<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CalculationLog;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\KMeansResultExport;

class ReportController extends Controller
{
    // 1. Halaman Utama Menu Laporan (Dengan Filter)
    public function index(Request $request)
    {
        $query = CalculationLog::query();

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        if ($request->filled('k_value')) {
            $query->where('k_value', $request->k_value);
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('admin.report.index', compact('logs'));
    }

    // 2. Export ke Excel
    public function exportExcel($log_id)
    {
        $log = CalculationLog::findOrFail($log_id);
        $fileName = 'Laporan_Klastering_Codero_' . $log->created_at->format('d-M-Y') . '.xlsx';

        return Excel::download(new KMeansResultExport($log_id), $fileName);
    }

    // 3. Export ke PDF
    public function exportPdf($log_id)
    {
        $log = CalculationLog::with('results')->findOrFail($log_id);
        $clusters = $log->results->groupBy('cluster_number');

        $pdf = Pdf::loadView('admin.kmeans.pdf', compact('log', 'clusters'));

        $fileName = 'Laporan_Klastering_Codero_' . $log->created_at->format('d-M-Y') . '.pdf';
        return $pdf->download($fileName);
    }
}
