<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class parkir_kendaraans extends Model
{
    protected $table = 'tb_kendaraan';
    protected $primaryKey = 'id_kendaraan';
    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'plat_nomor',
        'jenis_kendaraan',
        'warna',
        'pemilik'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(parkir_users::class, 'id_user', 'id_user');
    }

    public function transaksis(): HasMany
    {
        return $this->hasMany(parkir_transaksis::class, 'id_kendaraan', 'id_kendaraan');
    }
}