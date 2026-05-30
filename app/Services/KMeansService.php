<?php

namespace App\Services;

use Exception;

class KMeansService
{
    protected $dataset;
    protected $k;
    protected $initMethod;
    protected $manualCentroids;

    public function __construct($k, $dataset, $initMethod = 'sequential', $manualCentroids = [])
    {
        $this->k = $k;
        $this->dataset = $dataset;
        $this->initMethod = $initMethod;
        $this->manualCentroids = $manualCentroids;
    }

    public function run()
    {
        $studentIds = array_keys($this->dataset);
        $data = [];
        $criteriaKeys = [];

        foreach ($this->dataset as $scores) {
            if (empty($criteriaKeys)) {
                $criteriaKeys = array_keys($scores); // Simpan urutan kriteria (misal: V1, V2)
            }
            $data[] = array_values($scores);
        }

        $kValue = $this->k;
        $numPoints = count($data);
        if ($numPoints == 0) {
            throw new Exception("Dataset kosong.");
        }
        $numFeatures = count($data[0]);

        $centroids = [];
        // Inisialisasi centroid berdasarkan metode
        if ($this->initMethod === 'manual' && !empty($this->manualCentroids)) {
            foreach ($this->manualCentroids as $studentId) {
                $idx = array_search($studentId, $studentIds);
                if ($idx !== false) {
                    $centroids[] = $data[$idx];
                }
            }
            // Fallback jika tidak lengkap
            if (count($centroids) < $kValue) {
                $centroids = array_slice($data, 0, $kValue);
            }
        } elseif ($this->initMethod === 'random') {
            if ($numPoints >= $kValue) {
                $randomKeys = array_rand($data, $kValue);
                if (!is_array($randomKeys)) {
                    $randomKeys = [$randomKeys];
                }
                foreach ($randomKeys as $idx) {
                    $centroids[] = $data[$idx];
                }
            } else {
                $centroids = array_slice($data, 0, $kValue);
            }
        } else {
            // Default: sequential
            $centroids = array_slice($data, 0, $kValue);
        }

        $initialCentroidsInfo = [];
        foreach ($centroids as $j => $centroid) {
            $matchedIdx = array_search($centroid, $data);
            $studentId = ($matchedIdx !== false) ? $studentIds[$matchedIdx] : null;

            $cData = [];
            foreach ($centroid as $fIndex => $fValue) {
                $cData[$criteriaKeys[$fIndex]] = $fValue;
            }

            $initialCentroidsInfo[] = [
                'cluster' => $j + 1,
                'student_id' => $studentId,
                'scores' => $cData
            ];
        }

        $iterations = 0;
        $maxIterations = 300; // scikit-learn K-Means max_iter default
        $labels = array_fill(0, $numPoints, -1); // Initialize with -1
        $clusters = [];
        $history = [];

        while ($iterations < $maxIterations) {

            $clusters = array_fill(0, $kValue, []);
            $newLabels = array_fill(0, $numPoints, 0);

            // Assign points to nearest centroid
            for ($i = 0; $i < $numPoints; $i++) {
                $minDist = PHP_FLOAT_MAX;
                $closestCentroid = 0;

                for ($j = 0; $j < $kValue; $j++) {
                    $dist = $this->euclideanDistance($data[$i], $centroids[$j]);
                    if ($dist < $minDist) {
                        $minDist = $dist;
                        $closestCentroid = $j;
                    }
                }
                $newLabels[$i] = $closestCentroid;
                $clusters[$closestCentroid][] = $i;
            }

            // Check convergence
            if ($newLabels === $labels && $iterations > 0) {
                break;
            }

            $labels = $newLabels;

            // Update centroids
            $newCentroids = array_fill(0, $kValue, array_fill(0, $numFeatures, 0));
            for ($j = 0; $j < $kValue; $j++) {
                if (count($clusters[$j]) > 0) {
                    foreach ($clusters[$j] as $pointIdx) {
                        for ($f = 0; $f < $numFeatures; $f++) {
                            $newCentroids[$j][$f] += $data[$pointIdx][$f];
                        }
                    }
                    for ($f = 0; $f < $numFeatures; $f++) {
                        $newCentroids[$j][$f] /= count($clusters[$j]);
                    }
                }
            }

            // Record iteration details to history
            $iterationClusters = [];
            for ($j = 0; $j < $kValue; $j++) {
                $members = [];
                if (isset($clusters[$j])) {
                    foreach ($clusters[$j] as $idx) {
                        $members[] = $studentIds[$idx];
                    }
                }
                
                $cData = [];
                foreach ($newCentroids[$j] as $fIndex => $fValue) {
                    $cData[$criteriaKeys[$fIndex]] = $fValue;
                }

                $iterationClusters[] = [
                    'cluster' => $j + 1,
                    'members' => $members,
                    'new_centroid' => $cData
                ];
            }

            $history[] = [
                'iteration' => $iterations + 1,
                'clusters' => $iterationClusters
            ];

            $centroids = $newCentroids;
            $iterations++;
        }

        // Calculate DBI
        $dbi = $this->calculateDBI($data, $labels, $centroids, $kValue);

        // Format clusters
        $formattedClusters = [];
        for ($j = 0; $j < $kValue; $j++) {
            $formattedClusters[(string)$j] = [];
            if (isset($clusters[$j])) {
                foreach ($clusters[$j] as $idx) {
                    $formattedClusters[(string)$j][] = $studentIds[$idx];
                }
            }
        }

        // Kembalikan Centroid ke format asal agar Laravel tidak bingung
        $formattedCentroids = [];
        foreach ($centroids as $clusterIndex => $centroidValues) {
            $centroidData = [];
            foreach ($centroidValues as $index => $value) {
                $criterionCode = $criteriaKeys[$index];
                $centroidData[$criterionCode] = $value;
            }
            $formattedCentroids[$clusterIndex] = $centroidData;
        }

        // Kirim hasil lengkapnya ke Controller
        return [
            'clusters' => $formattedClusters,
            'centroids' => $formattedCentroids,
            'dbi' => $dbi,
            'iterations' => $iterations,
            'history' => [
                'initial_centroids' => $initialCentroidsInfo,
                'iterations' => $history,
                'final_centroids' => $formattedCentroids
            ],
        ];
    }

    private function euclideanDistance($p1, $p2)
    {
        $sum = 0;
        for ($i = 0; $i < count($p1); $i++) {
            $sum += pow($p1[$i] - $p2[$i], 2);
        }
        return sqrt($sum);
    }

    private function calculateDBI($data, $labels, $centroids, $kValue)
    {
        // 1. S: rata-rata jarak titik di dalam cluster ke centroidnya
        $S = array_fill(0, $kValue, 0);
        for ($i = 0; $i < $kValue; $i++) {
            $pointsInCluster = [];
            for ($j = 0; $j < count($data); $j++) {
                if ($labels[$j] == $i) {
                    $pointsInCluster[] = $data[$j];
                }
            }

            if (count($pointsInCluster) == 0) {
                $S[$i] = 0;
            } else {
                $sumDists = 0;
                foreach ($pointsInCluster as $p) {
                    $sumDists += $this->euclideanDistance($p, $centroids[$i]);
                }
                $S[$i] = $sumDists / count($pointsInCluster);
            }
        }

        // 2. M: Jarak antar centroid
        $M = array_fill(0, $kValue, array_fill(0, $kValue, 0));
        for ($i = 0; $i < $kValue; $i++) {
            for ($j = 0; $j < $kValue; $j++) {
                if ($i != $j) {
                    $M[$i][$j] = $this->euclideanDistance($centroids[$i], $centroids[$j]);
                }
            }
        }

        // 3. R: Ratio
        $R = array_fill(0, $kValue, array_fill(0, $kValue, 0));
        for ($i = 0; $i < $kValue; $i++) {
            for ($j = 0; $j < $kValue; $j++) {
                if ($i != $j) {
                    if ($M[$i][$j] == 0) {
                        $R[$i][$j] = 0;
                    } else {
                        $R[$i][$j] = ($S[$i] + $S[$j]) / $M[$i][$j];
                    }
                }
            }
        }

        // 4. D: Max R untuk setiap cluster
        $D = array_fill(0, $kValue, 0);
        for ($i = 0; $i < $kValue; $i++) {
            $maxR = 0;
            for ($j = 0; $j < $kValue; $j++) {
                if ($i != $j && $R[$i][$j] > $maxR) {
                    $maxR = $R[$i][$j];
                }
            }
            $D[$i] = $maxR;
        }

        // 5. DBI: Rata-rata dari D
        if ($kValue == 0) {
            return 0;
        }
        
        $sumD = 0;
        foreach ($D as $val) {
            $sumD += $val;
        }
        return $sumD / $kValue;
    }
}
