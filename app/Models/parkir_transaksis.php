<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class parkir_transaksis extends Model
{
    protected $fillable = [
        'durasi_jam',
        'biaya_total',
        'status'
    ];
}
