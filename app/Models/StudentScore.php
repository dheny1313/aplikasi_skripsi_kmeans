<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentScore extends Model
{
    //
    protected $table = 'student_score';

    protected $fillable = [
        'student_id',
        'criterion_id',
        'teacher_id',
        'score'
    ];

    // Tambahkan relasi ke User (Guru)
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function criterion()
    {
        return $this->belongsTo(Criterion::class);
    }
}
