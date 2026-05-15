<?php

namespace App\Services;

class ElbowService
{
    /**
     * Menjalankan simulasi K-Means dari K=1 sampai maxK untuk mencari WCSS
     */
    public function analyze(array $dataset, $maxK = 10)
    {
        // Jika jumlah siswa lebih sedikit dari maxK, batasi maxK sesuai jumlah siswa
        $totalData = count($dataset);
        if ($totalData < $maxK) {
            $maxK = $totalData;
        }

        $results = [];

        for ($k = 1; $k <= $maxK; $k++) {
            // Panggil mesin K-Means
            $kmeans = new KMeansService($k, $dataset);
            $clusteringResult = $kmeans->cluster();

            // Hitung WCSS untuk K saat ini
            $wcss = $this->calculateWCSS($dataset, $clusteringResult['final_clusters'], $clusteringResult['final_centroids'], $kmeans);

            $results[] = [
                'k' => $k,
                'wcss' => $wcss
            ];
        }

        return $results; // Mengembalikan array untuk digambar di Chart.js nanti
    }

    /**
     * Rumus penghitungan Within-Cluster Sum of Squares (WCSS)
     */
    protected function calculateWCSS($dataset, $clusters, $centroids, $kmeansInstance)
    {
        $wcss = 0;
        foreach ($clusters as $clusterIndex => $studentIds) {
            $centroid = $centroids[$clusterIndex];
            foreach ($studentIds as $id) {
                $scores = $dataset[$id];
                // Kuadratkan jarak Euclidean
                $squaredDistance = pow($kmeansInstance->calculateEuclideanDistance($scores, $centroid), 2);
                $wcss += $squaredDistance;
            }
        }
        return $wcss;
    }
}
