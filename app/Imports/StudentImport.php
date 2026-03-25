<?php

namespace App\Imports;

use App\Models\Student;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;


class StudentImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $rows
    */
    public function collection(Collection $rows)
    {
        // Kita looping (ulang) setiap baris dari file Excel secara manual
        foreach ($rows as $row) {
            
            // Cek perlindungan: Jika kolom NIS kosong di baris ini, lewati ke baris berikutnya
            if (!isset($row['nis']) || empty($row['nis'])) {
                continue; 
            }

            // Langsung eksekusi simpan ke database
            Student::updateOrCreate(
                // Kondisi pencarian (Cari siswa yang ID-nya sama dengan NIS di Excel)
                ['student_id' => $row['nis']], 
                
                // Data yang akan diupdate/dibuat
                [
                    'name' => $row['nama_siswa'],
                    'gender' => strtoupper($row['jk'] ?? 'L'), // Pastikan huruf besar
                    'is_active' => true,
                ]
            );
        }
    }
}