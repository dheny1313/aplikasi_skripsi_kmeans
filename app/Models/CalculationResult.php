<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalculationResult extends Model
{
    protected $table = 'calculation_result';

    protected $fillable = [
        'calculation_log_id',
        'cluster_number',
        'snapshot_data'
    ];

    protected $casts = [
        'snapshot_data' => 'array',
    ];

    public function log()
    {
        return $this->belongsTo(CalculationLog::class, 'calculation_log_id');
    }
}
