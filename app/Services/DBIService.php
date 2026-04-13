<?php

namespace App\Services;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class DBIService
{
    /**
     * Mengevaluasi hasil K-Means menggunakan Python scikit-learn
     */
    public function evaluate(array $dataset, array $clusters, array $centroids)
    {
        $k = count($clusters);
        if ($k <= 1) return 0; // DBI butuh minimal 2 klaster

        // 1. Susun ulang data Laravel agar formatnya disukai Python
        $X = [];
        $labels = [];

        foreach ($clusters as $clusterIndex => $studentIds) {
            foreach ($studentIds as $id) {
                // Ambil nilai fiturnya saja (V1, V2, dst)
                $X[] = array_values($dataset[$id]);
                // Catat siswa ini masuk klaster nomor berapa
                $labels[] = $clusterIndex;
            }
        }

        // 2. Tentukan lokasi file Python yang tadi kita buat
        $pythonScriptPath = storage_path('app/scripts/dbi_calculator.py');

        // 3. Bangun perintah untuk menjalankan Python di Terminal/CMD
        // Format: python lokasi_file.py "[data_X]" "[data_labels]"
        $process = new Process([
            'python', // atau 'python3' tergantung settingan environment laptop Anda
            $pythonScriptPath,
            json_encode($X),
            json_encode($labels)
        ]);

        // Eksekusi perintahnya!
        $process->run();

        // 4. Cek apakah Python mengalami error
        if (!$process->isSuccessful()) {
            // Jika Anda melihat angka 0 atau error, cek terminal log
            // throw new ProcessFailedException($process);
            return 0;
        }

        // 5. Tangkap angka yang di-print oleh Python
        $output = trim($process->getOutput());

        return floatval($output);
    }
}
