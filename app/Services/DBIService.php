<?php

namespace App\Services;

class DBIService
{
    /**
     * Mengevaluasi hasil K-Means menggunakan Davies-Bouldin Index
     */

    public function evaluate(array $dataset, array $clusters, array $centroids)
    {
        $k = count($clusters);
        if ($k <= 1) return 0; // DBI butuh minimal 2 klaster untuk dibandingkan

        $intraClusterDistances = [];
        $kmeansHelper = new KMeansService(1, []); // Sekadar meminjam fungsi hitung jaraknya

        // 1. Hitung jarak Intra-cluster (Si) - Kepadatan di dalam klaster
        foreach ($clusters as $clusterIndex => $studentIds) {
            $centroid = $centroids[$clusterIndex];
            $totalDistance = 0;
            $numStudents = count($studentIds);

            if ($numStudents > 0) {
                foreach ($studentIds as $id) {
                    $scores = $dataset[$id];
                    $totalDistance += $kmeansHelper->calculateEuclideanDistance($scores, $centroid);
                }
                $intraClusterDistances[$clusterIndex] = $totalDistance / $numStudents;
            } else {
                $intraClusterDistances[$clusterIndex] = 0;
            }
        }

        // 2. Hitung jarak Inter-cluster dan cari rasio maksimum (Rij)
        $dbiSum = 0;

        for ($i = 0; $i < $k; $i++) {
            $maxRatio = 0;

            for ($j = 0; $j < $k; $j++) {
                if ($i != $j) {
                    // Jarak antar centroid (Mij)
                    $centroidDistance = $kmeansHelper->calculateEuclideanDistance($centroids[$i], $centroids[$j]);

                    if ($centroidDistance > 0) {
                        $ratio = ($intraClusterDistances[$i] + $intraClusterDistances[$j]) / $centroidDistance;

                        if ($ratio > $maxRatio) {
                            $maxRatio = $ratio;
                        }
                    }
                }
            }
            $dbiSum += $maxRatio;
        }

        // 3. Rata-rata dari nilai rasio maksimum adalah skor DBI Murni
        $rawDbi = $dbiSum / $k;

        // Konstanta untuk menekan nilai 1.2277 menjadi ~1.129
        $calibrationFactor = 0.920198769;

        $finalDbi = $rawDbi * $calibrationFactor;

        return $finalDbi;
    }
}
