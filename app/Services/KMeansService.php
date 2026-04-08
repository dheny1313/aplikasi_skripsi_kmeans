<?php

namespace App\Services;

class KMeansService
{
    protected $k;
    protected $dataset;
    protected $centroids = [];
    protected $clusters = [];
    protected $maxIterations = 100;
    protected $history = [];

    public function __construct($k, array $dataset)
    {
        $this->k = $k;
        $this->dataset = $dataset;
    }

    public function cluster()
    {
        $this->initializeCentroids();
        $isConverged = false;
        $iteration = 0;

        while (!$isConverged && $iteration < $this->maxIterations) {
            $iteration++;
            $oldCentroids = $this->centroids;

            $this->assignToClusters();

            $this->history[] = [
                'iteration' => $iteration,
                'centroids' => $this->centroids,
                'clusters' => $this->clusters,
            ];

            $this->updateCentroids();

            if ($this->isConverged($oldCentroids, $this->centroids)) {
                $isConverged = true;
            }
        }

        return [
            'k' => $this->k,
            'total_iterations' => $iteration,
            'final_clusters' => $this->clusters,
            'final_centroids' => $oldCentroids,
            'history' => $this->history
        ];
    }

    protected function initializeCentroids()
    {
        // 1. Ambil semua ID siswa
        $studentIds = array_keys($this->dataset);

        // 2. KUNCI PERTAMA: Urutkan ID agar daftarnya selalu konsisten setiap kali dieksekusi
        sort($studentIds);

        // 3. KUNCI KEDUA: Tentukan jarak lompatan untuk memilih Centroid awal
        // Jika siswa ada 42 dan K=3, maka lompatannya adalah 42/3 = 14
        $step = max(1, floor(count($studentIds) / $this->k));

        for ($i = 0; $i < $this->k; $i++) {
            // Ambil siswa pada indeks ke-0, ke-14, dan ke-28 sebagai titik mula
            $index = $i * $step;

            // Mencegah error jika indeks melebihi jumlah data
            if ($index >= count($studentIds)) {
                $index = count($studentIds) - 1;
            }

            $id = $studentIds[$index];
            $this->centroids[$i] = $this->dataset[$id];
        }
    }
    
    protected function assignToClusters()
    {
        $this->clusters = array_fill(0, $this->k, []);

        foreach ($this->dataset as $studentId => $scores) {
            $minDistance = null;
            $closestClusterIndex = null;

            foreach ($this->centroids as $clusterIndex => $centroid) {
                $distance = $this->calculateEuclideanDistance($scores, $centroid);
                if ($minDistance === null || $distance < $minDistance) {
                    $minDistance = $distance;
                    $closestClusterIndex = $clusterIndex;
                }
            }
            $this->clusters[$closestClusterIndex][] = $studentId;
        }
    }

    public function calculateEuclideanDistance($dataPoint, $centroid)
    {
        $sum = 0;
        foreach ($centroid as $criterion => $value) {
            if (isset($dataPoint[$criterion])) {
                $sum += pow($dataPoint[$criterion] - $value, 2);
            }
        }
        return sqrt($sum);
    }

    protected function updateCentroids()
    {
        foreach ($this->clusters as $clusterIndex => $studentIds) {
            if (empty($studentIds)) continue;

            $newCentroid = [];
            $numStudents = count($studentIds);
            $sumScores = [];

            foreach ($studentIds as $id) {
                $scores = $this->dataset[$id];
                foreach ($scores as $criterion => $value) {
                    if (!isset($sumScores[$criterion])) $sumScores[$criterion] = 0;
                    $sumScores[$criterion] += $value;
                }
            }

            foreach ($sumScores as $criterion => $sum) {
                $newCentroid[$criterion] = $sum / $numStudents;
            }
            $this->centroids[$clusterIndex] = $newCentroid;
        }
    }

    protected function isConverged($oldCentroids, $newCentroids)
    {
        $epsilon = 0.0001;
        foreach ($oldCentroids as $clusterIndex => $oldCentroid) {
            $newCentroid = $newCentroids[$clusterIndex];
            foreach ($oldCentroid as $criterion => $oldValue) {
                if (abs($oldValue - $newCentroid[$criterion]) > $epsilon) {
                    return false;
                }
            }
        }
        return true;
    }
}
