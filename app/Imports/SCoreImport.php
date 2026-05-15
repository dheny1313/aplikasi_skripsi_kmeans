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

    protected $teacherId;
    // Tambahkan Constructor untuk menerima titipan ID Guru
    public function __construct($teacherId = null)
    {
        // Jika tidak ada ID yang dioper (misal saat guru import sendiri), gunakan ID yang sedang login
        $this->teacherId = $teacherId ?? auth()->id();
    }

    public function collection(Collection $rows)
    {
        $criteria = Criterion::all();

        foreach ($rows as $row) {
            //   dd($row->toArray());

            // Cek perlindungan: Jika kolom NIS kosong di baris ini, lewati ke baris berikutnya
            if (!isset($row['nis']) || empty($row['nis'])) {
                continue;
            }

            $student = Student::where('student_id', $row['nis'])->first();
            if (!$student) {
                continue;
            }

            foreach ($criteria as $criterion) {
                $headerCode = strtolower($criterion->code);

                if (isset($row[$headerCode]) && $row[$headerCode] !== '') {
                    $scoreValue = (int) $row[$headerCode];

                    if ($scoreValue >= 1 && $scoreValue <= 5) {
                        StudentScore::updateOrCreate(
                            [
                                'student_id' => $student->id,
                                'criterion_id' => $criterion->id,
                                // GUNAKAN VARIABEL DARI CONSTRUCTOR DI SINI:
                                'teacher_id' => $this->teacherId
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
