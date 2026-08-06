<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class parkir_users extends Model
{
    protected $fillable = [
        'password',
        'username',
        'nama_lengkap'
    ];
}
