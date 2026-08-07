<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class parkir_tarifs extends Model
{
    protected $table = 'tb_tarif';
    protected $primaryKey = 'id_tarif';
    public $timestamps = false;

    protected $fillable = [
        'jenis_kendaraan',
        'tarif_per_jam'
    ];

    public function transaksis(): HasMany
    {
        return $this->hasMany(parkir_transaksis::class, 'id_tarif', 'id_tarif');
    }
}