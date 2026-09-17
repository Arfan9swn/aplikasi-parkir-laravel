<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class parkir_reservasis extends Model
{
    protected $table = 'tb_reservasi';
    protected $primaryKey = 'id_reservasi';
    public $timestamps = false;

    protected $fillable = [
        'id_area',
        'plat_nomor',
        'pemilik',
        'kontak',
        'waktu_datang',
        'status',
        'waktu_pengajuan',
    ];

    protected $casts = [
        'waktu_datang'    => 'datetime',
        'waktu_pengajuan' => 'datetime',
    ];

    public function area(): BelongsTo
    {
        return $this->belongsTo(parkir_areas::class, 'id_area', 'id_area');
    }
}
