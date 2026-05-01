<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NormalizedScore extends Model
{
    protected $fillable = ['student_id', 'normalized_data'];
    protected $casts = [
        'normalized_data' => 'array', // Agar otomatis jadi Array saat ditarik ke PHP
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
