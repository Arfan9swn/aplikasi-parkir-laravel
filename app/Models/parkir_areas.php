<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class parkir_areas extends Model
{
    protected $fillable = [
        'nama_area',
        'kapasitas',
        'terisi'
    ];
}
