<?php

namespace App\Models;

use App\Notifications\VerifyEmailOtp;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class parkir_users extends Model
{
    use Notifiable;

    public const OTP_TTL_MINUTES = 10;

    protected $table = 'tb_user';
    protected $primaryKey = 'id_user';
    public $timestamps = false;

    protected $fillable = [
        'nama_lengkap',
        'email',
        'username',
        'password',
        'role',
        'status_aktif',
        'status_verifikasi'
    ];

    protected $hidden = [
        'password',
        'otp_code'
    ];

    protected $casts = [
        'otp_expires_at'    => 'datetime',
        'email_verified_at' => 'datetime',
    ];

    public function isVerified(): bool
    {
        return $this->status_verifikasi === 'diterima';
    }

    public function hasVerifiedEmail(): bool
    {
        return $this->email_verified_at !== null;
    }

    /**
     * Rotate the signup OTP and mail it to the account's email. The code is
     * stored hashed; the plaintext only exists inside the notification.
     */
    public function issueEmailOtp(): void
    {
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $this->forceFill([
            'otp_code'       => Hash::make($code),
            'otp_expires_at' => now()->addMinutes(self::OTP_TTL_MINUTES),
        ])->save();

        $this->notify(new VerifyEmailOtp($code));
    }

    public function kendaraan(): HasMany
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