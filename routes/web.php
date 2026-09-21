<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KeluarController;
use App\Http\Controllers\KendaraanController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\MasukController;
use App\Http\Controllers\ReservasiController;
use App\Http\Controllers\TarifController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Public — everything a guest (not logged in) may do
|--------------------------------------------------------------------------
| Guests can only browse the landing page, look at the areas and which
| vehicles are inside them, and submit a reservation request. Anything
| else is gated behind the auth.web middleware below.
*/
Route::get('/', [BerandaController::class, 'index'])->name('beranda');

Route::get('/area', [AreaController::class, 'index'])->name('ticket.area');
Route::get('/area/{area}', [AreaController::class, 'show'])->whereNumber('area')->name('ticket.area.show');

Route::get('/reservasi', [ReservasiController::class, 'create'])->name('reservasi.create');
Route::post('/reservasi', [ReservasiController::class, 'store'])->name('reservasi.store');

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/
Route::get('/login', function () {
    if (session('auth_user')) {
        return redirect()->route('beranda');
    }
    return view('auth.login');
})->name('login');

Route::post('/login', [AuthController::class, 'loginWeb']);

Route::get('/registrasi', function () {
    if (session('auth_user')) {
        return redirect()->route('beranda');
    }
    return view('auth.register');
})->name('register');

Route::post('/registrasi', [AuthController::class, 'registerWeb']);

Route::post('/logout', [AuthController::class, 'logoutWeb'])->name('logout');

/*
|--------------------------------------------------------------------------
| Staff — requires a logged-in session
|--------------------------------------------------------------------------
*/
Route::middleware('auth.web')->group(function () {
    Route::middleware('role:petugas,owner')->group(function () {
        Route::get('/masuk', [MasukController::class, 'create'])->name('ticket.masuk');
        Route::post('/masuk', [MasukController::class, 'store']);

        Route::get('/keluar', [KeluarController::class, 'create'])->name('ticket.keluar');
        Route::get('/keluar/{ticket}', [KeluarController::class, 'show'])->whereNumber('ticket')->name('ticket.keluar.show');
        Route::post('/keluar/check', [KeluarController::class, 'check']);
        Route::post('/keluar/pay', [KeluarController::class, 'pay']);

        Route::get('/transaksi', [TransaksiController::class, 'index'])->name('ticket.index');

        Route::get('/area/create', [AreaController::class, 'create'])->name('ticket.area.create');
        Route::post('/area', [AreaController::class, 'store']);
        Route::get('/area/{area}/edit', [AreaController::class, 'edit'])->name('ticket.area.edit');
        Route::put('/area/{area}', [AreaController::class, 'update']);
        Route::delete('/area/{area}', [AreaController::class, 'destroy']);

        Route::get('/kendaraan', [KendaraanController::class, 'index'])->name('ticket.kendaraan');
        Route::get('/kendaraan/{kendaraan}', [KendaraanController::class, 'show'])->whereNumber('kendaraan')->name('ticket.kendaraan.show');
        Route::get('/kendaraan/create', [KendaraanController::class, 'create'])->name('ticket.kendaraan.create');
        Route::post('/kendaraan', [KendaraanController::class, 'store']);
        Route::get('/kendaraan/{kendaraan}/edit', [KendaraanController::class, 'edit'])->name('ticket.kendaraan.edit');
        Route::put('/kendaraan/{kendaraan}', [KendaraanController::class, 'update']);
        Route::delete('/kendaraan/{kendaraan}', [KendaraanController::class, 'destroy']);

        /*
        |jenis & tarif — tb_tarif is the source of truth for the types the gate
        |accepts, so managing it means managing the vehicle types themselves.
        */
        Route::get('/tarif', [TarifController::class, 'index'])->name('ticket.tarif');
        Route::get('/tarif/create', [TarifController::class, 'create'])->name('ticket.tarif.create');
        Route::post('/tarif', [TarifController::class, 'store']);
        Route::get('/tarif/{tarif}/edit', [TarifController::class, 'edit'])->name('ticket.tarif.edit');
        Route::put('/tarif/{tarif}', [TarifController::class, 'update']);
        Route::delete('/tarif/{tarif}', [TarifController::class, 'destroy']);

        /*
        | Reservation review — guests submit from /reservasi, staff process here.
        */
        Route::get('/reservasi/daftar', [ReservasiController::class, 'index'])->name('reservasi.index');
        Route::post('/reservasi/{reservasi}/konfirmasi', [ReservasiController::class, 'confirm'])->whereNumber('reservasi')->name('reservasi.confirm');
        Route::post('/reservasi/{reservasi}/batal', [ReservasiController::class, 'cancel'])->whereNumber('reservasi')->name('reservasi.cancel');
    });

    /*
    | Admin panel — role admin manages accounts only (plus the system logs).
    */
    Route::middleware('role:admin')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::put('/users/{user}/role', [UserController::class, 'updateRole'])->whereNumber('user')->name('users.role');
        Route::put('/users/{user}/password', [UserController::class, 'updatePassword'])->whereNumber('user')->name('users.password');
        Route::put('/users/{user}/profile', [UserController::class, 'updateProfile'])->whereNumber('user')->name('users.profile');
    });

    Route::get('/log', [LogController::class, 'index'])->name('log');
    Route::get('/log-aktivitas', [LogController::class, 'index']);
});
