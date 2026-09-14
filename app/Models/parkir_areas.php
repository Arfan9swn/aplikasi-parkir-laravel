<?php

namespace App\Models;

use App\Models\parkir_users;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class parkir_areas extends Model
{
    protected $table = 'tb_area_parkir';
    protected $primaryKey = 'id_area';
    public $timestamps = false;

    protected $fillable = [
        'nama_area',
        'kapasitas',
        'terisi',
        'id_user'
    ];

    public function transaksis(): HasMany
    {
        return $this->hasMany(parkir_transaksis::class, 'id_area', 'id_area');
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(parkir_users::class, 'id_user', 'id_user');
    }
}