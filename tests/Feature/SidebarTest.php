<?php

use Illuminate\Support\ViewErrorBag;

beforeEach(function () {
    $this->withoutVite();
});

test('guests see an accessible sidebar with public links and account actions', function () {
    $this->view('layouts.app', ['errors' => new ViewErrorBag])
        ->assertSee('id="app-sidebar"', false)
        ->assertSee('id="sidebar-toggle"', false)
        ->assertSee('role="tooltip"', false)
        ->assertSee('aria-current="page"', false)
        ->assertSee('href="/reservasi"', false)
        ->assertSee('data-tooltip="Daftar"', false)
        ->assertDontSee('href="/transaksi"', false)
        ->assertDontSee('name="_token"', false);
});

test('staff retain their navigation profile and csrf protected logout', function () {
    session(['auth_user' => ['nama' => 'Petugas Parkir', 'role' => 'petugas']]);

    $this->view('layouts.app', ['errors' => new ViewErrorBag])
        ->assertSee('Petugas Parkir')
        ->assertSee('href="/masuk"', false)
        ->assertSee('href="/keluar"', false)
        ->assertSee('href="/transaksi"', false)
        ->assertSee('href="/reservasi/daftar"', false)
        ->assertSee('href="/log"', false)
        ->assertSee('name="_token"', false)
        ->assertSee('data-tooltip="Keluar dari akun"', false)
        ->assertDontSee('data-tooltip="Daftar"', false);
});

test('admins only see the account management and log navigation', function () {
    session(['auth_user' => ['nama' => 'Admin Sistem', 'role' => 'admin']]);

    $this->view('layouts.app', ['errors' => new ViewErrorBag])
        ->assertSee('Admin Sistem')
        ->assertSee('href="/users"', false)
        ->assertSee('href="/log"', false)
        ->assertDontSee('href="/masuk"', false)
        ->assertDontSee('href="/keluar"', false)
        ->assertDontSee('href="/transaksi"', false)
        ->assertDontSee('href="/area"', false)
        ->assertDontSee('href="/kendaraan"', false)
        ->assertDontSee('href="/tarif"', false)
        ->assertDontSee('href="/reservasi/daftar"', false);
});
