<?php

namespace App\Exports;

use App\Models\CalculationLog;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Http\Controllers\Admin\ReportController;

class KMeansResultExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $log_id;

    // Menangkap ID Log dari Controller
    public function __construct($log_id)
    {
        $this->log_id = $log_id;
    }

    // Mengambil data dari database
    public function collection()
    {
        $log = CalculationLog::with('results')->findOrFail($this->log_id);
        // Urutkan berdasarkan nomor klaster agar di Excel rapi
        return $log->results->sortBy('cluster_number');
    }

    // Membuat Baris Pertama (Judul Kolom) di Excel
    public function headings(): array
    {
        return [
            'Nomor Klaster',
            'NIS Siswa',
            'Nama Lengkap Siswa',
            'Rata-rata Nilai (Multi-Rater)'
        ];
    }

    // Memetakan isi data ke masing-masing kolom Excel
    public function map($result): array
    {
        // Format nilai skor dari array/JSON menjadi teks yang mudah dibaca
        $scoresText = '';
        if (isset($result->snapshot_data['scores'])) {
            foreach ($result->snapshot_data['scores'] as $code => $val) {
                $scoresText .= strtoupper($code) . ': ' . round($val, 2) . ' | ';
            }
        }

        return [
            'Klaster ' . $result->cluster_number,
            $result->snapshot_data['nis'] ?? '-',
            $result->snapshot_data['name'] ?? 'Data Tidak Diketahui',
            rtrim($scoresText, ' | ') // Hilangkan garis pemisah di ujung teks
        ];
    }

    // Memberikan gaya (style) pada Excel, misalnya menebalkan baris pertama
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]], // Header dicetak tebal
        ];
    }
}
