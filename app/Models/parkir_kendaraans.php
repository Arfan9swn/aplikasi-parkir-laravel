<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class parkir_kendaraans extends Model
{
    protected $fillable = [
        'plat_nomor',
        'jenis_kendaraan',
        'warna',
        'pemilik'
    ];
}
