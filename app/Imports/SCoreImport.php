<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\Student;
use App\Models\Criterion;
use App\Models\StudentScore;
use Maatwebsite\Excel\Concerns\WithHeadingRow;


class SCoreImport implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        // 1. Ambil semua kriteria yang ada di sistem
        $criteria = Criterion::all();

        // 2. Looping baris demi baris dari file Excel
        foreach ($rows as $row) {
            // Hentikan sistem dan tampilkan apa yang dibaca oleh Laravel dari Excel!
            //dd($row->toArray());

            // Lewati jika kolom 'nis' kosong
            if (!isset($row['nis']) || empty($row['nis'])) {
                continue;
            }

            // Cari siswa berdasarkan NIS
            $student = Student::where('student_id', $row['nis'])->first();
            if (!$student) {
                continue; // Jika NIS tidak terdaftar di database, lewati
            }

            // 3. Looping kriteria untuk mencari nilainya di kolom Excel
            foreach ($criteria as $criterion) {
                // Laravel Excel otomatis mengubah nama header menjadi huruf kecil (contoh: 'C1' menjadi 'c1')
                $headerCode = strtolower($criterion->code);

                // Cek apakah kolom kriteria tersebut ada di Excel dan tidak kosong
                if (isset($row[$headerCode]) && $row[$headerCode] !== '') {

                    $scoreValue = (int) $row[$headerCode];

                    // Validasi: Pastikan nilai yang diinput di Excel adalah skala 1 sampai 5
                    if ($scoreValue >= 1 && $scoreValue <= 5) {
                        StudentScore::updateOrCreate(
                            [
                                'student_id' => $student->id,
                                'criterion_id' => $criterion->id,
                                'teacher_id' => auth()->id() // KUNCI UTAMANYA DI SINI'
                            ],
                            [
                                'score' => $scoreValue
                            ]
                        );
                    }
                }
            }
        }
    }
}
