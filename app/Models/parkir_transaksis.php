<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class parkir_transaksis extends Model
{
    protected $table = 'tb_transaksi';
    protected $primaryKey = 'id_parkir';
    public $timestamps = false;

    protected $fillable = [
        'id_kendaraan',
        'id_tarif',
        'id_user',
        'id_area',
        'waktu_masuk',
        'waktu_keluar',
        'durasi_jam',
        'biaya_total',
        'status'
    ];

    protected $casts = [
        'waktu_masuk' => 'datetime',
        'waktu_keluar' => 'datetime',
        'biaya_total' => 'decimal:2'
    ];

    public function kendaraan(): BelongsTo
    {
        return $this->belongsTo(parkir_kendaraans::class, 'id_kendaraan', 'id_kendaraan');
    }

    public function tarif(): BelongsTo
    {
        return $this->belongsTo(parkir_tarifs::class, 'id_tarif', 'id_tarif');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(parkir_users::class, 'id_user', 'id_user');
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(parkir_areas::class, 'id_area', 'id_area');
    }
}