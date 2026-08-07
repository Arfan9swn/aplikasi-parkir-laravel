<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class parkir_logs extends Model
{
    protected $table = 'tb_log_aktivitas';
    protected $primaryKey = 'id_log';
    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'aktivitas',
        'waktu_aktivitas'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(parkir_users::class, 'id_user', 'id_user');
    }
}