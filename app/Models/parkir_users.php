<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class parkir_users extends Model
{
    protected $table = 'tb_user';
    protected $primaryKey = 'id_user';
    public $timestamps = false;

    protected $fillable = [
        'nama_lengkap',
        'username',
        'password',
        'role',
        'status_aktif'
    ];

    protected $hidden = [
        'password'
    ];

    public function kendaraans(): HasMany
    {
        return $this->hasMany(parkir_kendaraans::class, 'id_user', 'id_user');
    }

    public function transaksis(): HasMany
    {
        return $this->hasMany(parkir_transaksis::class, 'id_user', 'id_user');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(parkir_logs::class, 'id_user', 'id_user');
    }
}