<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CriterionScale extends Model
{
    protected $fillable = ['criterion_id', 'scale_value', 'description'];

    // Relasi balik ke Kriteria
    public function criterion()
    {
        return $this->belongsTo(Criterion::class);
    }
    //
}
