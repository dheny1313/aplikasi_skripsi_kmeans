<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Criterion extends Model
{
    //
    protected $table = 'criteria';

    protected $fillable = ['code', 'name', 'weight'];

    public function scores()
    {
        return $this->hasMany(StudentScore::class);
    }

    // TAMBAHKAN INI: Relasi Satu Kriteria punya Banyak Skala (Rubrik)
    public function scales()
    {
        return $this->hasMany(CriterionScale::class);
    }
}
