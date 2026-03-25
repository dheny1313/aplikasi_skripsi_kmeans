<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $table = 'student';

    protected $fillable = ['student_id', 'name', 'gender', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scores()
    {
        return $this->hasMany(StudentScore::class);
    }
}
