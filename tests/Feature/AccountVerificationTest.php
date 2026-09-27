<?php

use App\Models\parkir_users;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->withoutVite();
});

function createVerificationAccount(array $overrides = []): parkir_users
{
    $data = array_merge([
        'nama_lengkap'     => 'Petugas Uji',
        'username'         => 'petugasuji',
        'password'         => Hash::make('password123'),
        'role'             => 'petugas',
        'status_aktif'     => 1,
        'status_verifikasi' => 'menunggu',
    ], $overrides);

    $data['email'] ??= $data['username'] . '@uji.test';

    return parkir_users::create($data);
}

function createVerificationAdmin(): parkir_users
{
    return createVerificationAccount([
        'nama_lengkap'     => 'Admin Uji',
        'username'         => 'adminuji',
        'role'             => 'admin',
        'status_verifikasi' => 'diterima',
    ]);
}

function verificationSession(parkir_users $user): array
{
    return [
        'id_user'  => $user->id_user,
        'nama'     => $user->nama_lengkap,
        'username' => $user->username,
        'role'     => $user->role,
    ];
}

test('the registration page tells visitors an admin must approve the account', function () {
    $this->get('/registrasi')
        ->assertOk()
        ->assertSee('menunggu')
        ->assertSee('name="email"', false);
});

test('web registration creates a waiting account instead of an active worker', function () {
    $response = $this->post('/registrasi', [
        'nama_lengkap'          => 'Budi Santoso',
        'email'                 => 'budi@contoh.test',
        'username'              => 'budiuji',
        'password'              => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $user = parkir_users::where('username', 'budiuji')->firstOrFail();

    expect($user->role)->toBe('petugas')
        ->and($user->email)->toBe('budi@contoh.test')
        ->and((int) $user->status_aktif)->toBe(1)
        ->and($user->status_verifikasi)->toBe('menunggu');

    $response->assertRedirect(route('verifikasi.menunggu'));
    expect(session('auth_user.status_verifikasi'))->toBe('menunggu');
});

test('api registration also creates a waiting account', function () {
    $response = $this->postJson('/api/register', [
        'nama_lengkap'          => 'Akun Api',
        'email'                 => 'api@contoh.test',
        'username'              => 'akunapi',
        'password'              => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertStatus(201);

    $user = parkir_users::where('username', 'akunapi')->firstOrFail();
    expect($user->email)->toBe('api@contoh.test')
        ->and($user->status_verifikasi)->toBe('menunggu');
});

test('a waiting account can log in but only reaches the waiting page', function () {
    $user = createVerificationAccount();

    $this->post('/login', ['username' => 'petugasuji', 'password' => 'password123'])
        ->assertRedirect(route('verifikasi.menunggu'));

    $this->withSession(['auth_user' => verificationSession($user)])
        ->get('/masuk')
        ->assertRedirect(route('verifikasi.menunggu'));

    $this->withSession(['auth_user' => verificationSession($user)])
        ->get('/verifikasi')
        ->assertOk()
        ->assertSee('Menunggu Persetujuan')
        ->assertSee('petugasuji');
});

test('waiting accounts are kept out of staff pages and staff api routes', function () {
    $user = createVerificationAccount();

    $this->withSession(['auth_user' => verificationSession($user)])
        ->get('/log')
        ->assertRedirect(route('verifikasi.menunggu'));

    $this->withSession(['auth_user' => verificationSession($user)])
        ->get('/users')
        ->assertRedirect(route('verifikasi.menunggu'));

    $this->withSession(['auth_user' => verificationSession($user)])
        ->postJson('/api/areas', ['nama_area' => 'Area Uji', 'kapasitas' => 5])
        ->assertStatus(403)
        ->assertJson(['success' => false]);
});

test('the sidebar shows only public links while the account waits', function () {
    $user = createVerificationAccount();

    $this->withSession(['auth_user' => verificationSession($user)])
        ->get('/verifikasi')
        ->assertOk()
        ->assertSee('href="/reservasi"', false)
        ->assertDontSee('href="/masuk"', false)
        ->assertDontSee('href="/transaksi"', false);
});

test('admin approval unlocks the worker panel without a new login', function () {
    $admin  = createVerificationAdmin();
    $worker = createVerificationAccount();
    $stale  = verificationSession($worker);

    $this->withSession(['auth_user' => verificationSession($admin)])
        ->put(route('pengguna.verifikasi', $worker->id_user), ['aksi' => 'terima'])
        ->assertRedirect(route('pengguna.index'));

    expect($worker->fresh()->status_verifikasi)->toBe('diterima');

    // The old session still says menunggu; the gate re-reads the database.
    $this->withSession(['auth_user' => $stale])
        ->get('/masuk')
        ->assertOk()
        ->assertSee('href="/transaksi"', false);

    expect(session('auth_user.status_verifikasi'))->toBe('diterima');
});

test('a rejected registration is told on the waiting page and stays locked', function () {
    $admin  = createVerificationAdmin();
    $worker = createVerificationAccount();

    $this->withSession(['auth_user' => verificationSession($admin)])
        ->put(route('pengguna.verifikasi', $worker->id_user), ['aksi' => 'tolak'])
        ->assertRedirect(route('pengguna.index'));

    expect($worker->fresh()->status_verifikasi)->toBe('ditolak');

    $this->withSession(['auth_user' => verificationSession($worker)])
        ->get('/verifikasi')
        ->assertOk()
        ->assertSee('Pendaftaran Ditolak');

    $this->withSession(['auth_user' => verificationSession($worker)])
        ->get('/masuk')
        ->assertRedirect(route('verifikasi.menunggu'));
});

test('the account page lists waiting accounts with approve and reject actions', function () {
    $admin = createVerificationAdmin();
    createVerificationAccount();

    $this->withSession(['auth_user' => verificationSession($admin)])
        ->get('/users')
        ->assertOk()
        ->assertSee('menunggu verifikasi')
        ->assertSee('akun menunggu persetujuan')
        ->assertSee('name="aksi" value="terima"', false)
        ->assertSee('name="aksi" value="tolak"', false);
});

test('only admins can verify registrations', function () {
    $worker = createVerificationAccount(['username' => 'korbanguji']);
    $other  = createVerificationAccount([
        'username'         => 'petugaslagi',
        'status_verifikasi' => 'diterima',
    ]);

    $this->withSession(['auth_user' => verificationSession($other)])
        ->put(route('pengguna.verifikasi', $worker->id_user), ['aksi' => 'terima'])
        ->assertRedirect(route('beranda'));

    expect($worker->fresh()->status_verifikasi)->toBe('menunggu');
});

test('registration rejects a missing or malformed email', function () {
    $base = [
        'nama_lengkap'          => 'Uji Email',
        'password'              => 'password123',
        'password_confirmation' => 'password123',
    ];

    $this->post('/registrasi', array_merge($base, ['username' => 'tanpaemail']))
        ->assertSessionHasErrors('email');

    $this->post('/registrasi', array_merge($base, ['username' => 'emailrusak', 'email' => 'bukan-email']))
        ->assertSessionHasErrors('email');

    expect(parkir_users::count())->toBe(0);
});

test('the admin profile editor saves a new email', function () {
    $admin  = createVerificationAdmin();
    $worker = createVerificationAccount();

    $this->withSession(['auth_user' => verificationSession($admin)])
        ->put(route('pengguna.profile', $worker->id_user), [
            'username'     => 'petugasuji',
            'nama_lengkap' => 'Petugas Uji',
            'email'        => 'baru@contoh.test',
        ])
        ->assertRedirect(route('pengguna.index'));

    expect($worker->fresh()->email)->toBe('baru@contoh.test');
});