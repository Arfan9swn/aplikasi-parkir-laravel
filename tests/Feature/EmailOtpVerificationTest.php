<?php

use App\Models\parkir_users;
use App\Notifications\VerifyEmailOtp;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    $this->withoutVite();
    Notification::fake();
});

function otpRegistrationPayload(array $overrides = []): array
{
    return array_merge([
        'nama_lengkap'          => 'Uji OTP',
        'email'                 => 'otp@contoh.test',
        'username'              => 'otpuji',
        'password'              => 'password123',
        'password_confirmation' => 'password123',
    ], $overrides);
}

function capturedOtpFor(parkir_users $user): string
{
    $code = null;

    Notification::assertSentTo($user, VerifyEmailOtp::class, function ($notification) use (&$code) {
        $code = $notification->code;

        return true;
    });

    expect($code)->toMatch('/^[0-9]{6}$/');

    return $code;
}

test('registration emails a 6-digit otp instead of trusting the address', function () {
    $this->post('/registrasi', otpRegistrationPayload())
        ->assertRedirect(route('verifikasi.menunggu'));

    $user = parkir_users::where('username', 'otpuji')->firstOrFail();

    expect($user->otp_code)->not->toBeNull()
        ->and($user->otp_expires_at->isFuture())->toBeTrue()
        ->and($user->email_verified_at)->toBeNull();

    Notification::assertSentTo($user, VerifyEmailOtp::class);
    capturedOtpFor($user);
});

test('the waiting page takes the code, then the admin can approve', function () {
    $this->post('/registrasi', otpRegistrationPayload());
    $user = parkir_users::where('username', 'otpuji')->firstOrFail();
    $code = capturedOtpFor($user);

    $this->withSession(['auth_user' => verificationSession($user)])
        ->get('/verifikasi')
        ->assertOk()
        ->assertSee('name="otp"', false)
        ->assertSee('Verifikasi Email');

    // Step one: the correct code marks the email verified.
    $this->withSession(['auth_user' => verificationSession($user)])
        ->post('/verifikasi/otp', ['otp' => $code])
        ->assertRedirect(route('verifikasi.menunggu'));

    $user->refresh();
    expect($user->email_verified_at)->not->toBeNull()
        ->and($user->otp_code)->toBeNull();

    // Step two: only now does admin approval go through.
    $admin = createVerificationAdmin();
    $this->withSession(['auth_user' => verificationSession($admin)])
        ->put(route('pengguna.verifikasi', $user->id_user), ['aksi' => 'terima'])
        ->assertRedirect(route('pengguna.index'));

    expect($user->fresh()->status_verifikasi)->toBe('diterima');
});

test('wrong and expired codes never verify the email', function () {
    $this->post('/registrasi', otpRegistrationPayload());
    $user = parkir_users::where('username', 'otpuji')->firstOrFail();
    $code = capturedOtpFor($user);
    $wrong = $code === '000000' ? '000001' : '000000';

    $this->withSession(['auth_user' => verificationSession($user)])
        ->post('/verifikasi/otp', ['otp' => $wrong])
        ->assertSessionHasErrors('otp');

    expect($user->fresh()->email_verified_at)->toBeNull();

    $user->forceFill(['otp_expires_at' => now()->subMinute()])->save();

    $this->withSession(['auth_user' => verificationSession($user)])
        ->post('/verifikasi/otp', ['otp' => $code])
        ->assertSessionHasErrors('otp');

    expect($user->fresh()->email_verified_at)->toBeNull();
});

test('resending rotates the code so the old one stops working', function () {
    $this->post('/registrasi', otpRegistrationPayload());
    $user = parkir_users::where('username', 'otpuji')->firstOrFail();
    $old  = capturedOtpFor($user);
    $hash = $user->otp_code;

    $this->withSession(['auth_user' => verificationSession($user)])
        ->get('/verifikasi')
        ->assertOk();

    $this->withSession(['auth_user' => verificationSession($user)])
        ->post('/verifikasi/otp/kirim-ulang')
        ->assertRedirect(route('verifikasi.menunggu'));

    Notification::assertSentToTimes($user, VerifyEmailOtp::class, 2);
    expect($user->fresh()->otp_code)->not->toBe($hash);

    $this->withSession(['auth_user' => verificationSession($user)])
        ->post('/verifikasi/otp', ['otp' => $old])
        ->assertSessionHasErrors('otp');
});

test('the admin cannot approve until the email is verified', function () {
    $admin = createVerificationAdmin();
    $this->post('/registrasi', otpRegistrationPayload());
    $user = parkir_users::where('username', 'otpuji')->firstOrFail();

    $this->withSession(['auth_user' => verificationSession($admin)])
        ->put(route('pengguna.verifikasi', $user->id_user), ['aksi' => 'terima'])
        ->assertRedirect(route('pengguna.index'));

    expect($user->fresh()->status_verifikasi)->toBe('menunggu');

    $this->withSession(['auth_user' => verificationSession($admin)])
        ->get('/users')
        ->assertOk()
        ->assertSee('email belum terverifikasi')
        ->assertDontSee('value="terima"', false);

    $code = capturedOtpFor($user);
    $this->withSession(['auth_user' => verificationSession($user)])
        ->post('/verifikasi/otp', ['otp' => $code])
        ->assertRedirect(route('verifikasi.menunggu'));

    $this->withSession(['auth_user' => verificationSession($admin)])
        ->get('/users')
        ->assertSee('value="terima"', false);
});