<?php

namespace App\Services;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Exception;

class KMeansService
{
    protected $dataset;
    protected $k;

    public function __construct($k, $dataset)
    {
        $this->k = $k;
        $this->dataset = $dataset;
    }

    public function runPythonEngine()
    {
        // 1. Siapkan data nilai murni untuk Python (buang nama kriteria, ambil angkanya saja)
        $pythonData = [];
        $criteriaKeys = [];

        foreach ($this->dataset as $studentId => $scores) {
            if (empty($criteriaKeys)) {
                $criteriaKeys = array_keys($scores); // Simpan urutan kriteria (misal: V1, V2)
            }
            $pythonData[$studentId] = array_values($scores);
        }

        // 2. Bungkus nilai K dan Data menjadi teks JSON
        $payload = json_encode([
            'k' => $this->k,
            'data' => $pythonData
        ]);

        // 3. Tentukan letak file Python
        $pythonScriptPath = storage_path('app/scripts/kmeans_engine.py');

        // 4. Jalankan Python lewat Terminal (Ganti 'python' menjadi 'python3' jika Anda pakai Mac/Linux)
        // 4. Jalankan Python dengan membawa Environment Variable Windows (SystemRoot)
        $process = new Process(
            ['python', $pythonScriptPath, $payload],
            null, // working directory default
            [
                'SystemRoot' => getenv('SystemRoot') ?: 'C:\WINDOWS',
                'PATH' => getenv('PATH')
            ]
        );
        $process->run();


        // 5. Cek jika terjadi kegagalan sistem
        if (!$process->isSuccessful()) {
            throw new Exception("Gagal menjalankan Python: " . $process->getErrorOutput());
        }

        // 6. Tangkap dan Terjemahkan jawaban JSON dari Python
        $response = json_decode(trim($process->getOutput()), true);

        if (isset($response['status']) && $response['status'] === 'error') {
            throw new Exception("Error dari Python: " . $response['message']);
        }

        // 7. Kembalikan Centroid ke format asal agar Laravel tidak bingung
        $formattedCentroids = [];
        foreach ($response['centroids'] as $clusterIndex => $centroidValues) {
            $centroidData = [];
            foreach ($centroidValues as $index => $value) {
                $criterionCode = $criteriaKeys[$index];
                $centroidData[$criterionCode] = $value;
            }
            $formattedCentroids[$clusterIndex] = $centroidData;
        }

        // 8. Kirim hasil lengkapnya ke Controller
        return [
            'clusters' => $response['clusters'],
            'centroids' => $formattedCentroids,
            'dbi' => $response['dbi'],
            'iterations' => $response['iterations'],
        ];
    }
}
