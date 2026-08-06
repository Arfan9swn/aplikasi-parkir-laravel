<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class parkir_tarifs extends Model
{
    protected $fillable = [
        'jenis_kendaraan',
        'tarif_per_jam'
    ];
}
