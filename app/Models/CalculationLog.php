<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalculationLog extends Model
{
    //

    protected $table = 'calculation_logs';

    protected $fillable = [
        'user_id',
        'k_value',
        'dbi_score',
        'total_iterations',
        'description',
        'iteration_history'
    ];

    protected $casts = [
        'iteration_history' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function results()
    {
        return $this->hasMany(CalculationResult::class);
    }
}
