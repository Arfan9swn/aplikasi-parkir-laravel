<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class parkir_areas extends Model
{
    protected $table = 'tb_area_parkir';
    protected $primaryKey = 'id_area';
    public $timestamps = false;

    protected $fillable = [
        'nama_area',
        'kapasitas',
        'terisi'
    ];

    public function transaksis(): HasMany
    {
        return $this->hasMany(parkir_transaksis::class, 'id_area', 'id_area');
    }
}