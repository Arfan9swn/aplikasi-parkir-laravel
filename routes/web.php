<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KeluarController;
use App\Http\Controllers\KendaraanController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\MasukController;
use App\Http\Controllers\TransaksiController;

Route::get('/', [BerandaController::class, 'index'])->name('beranda');

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

Route::get('/masuk', [MasukController::class, 'create'])->name('ticket.masuk');
Route::post('/masuk', [MasukController::class, 'store']);

Route::get('/keluar', [KeluarController::class, 'create'])->name('ticket.keluar');
Route::get('/keluar/{ticket}', [KeluarController::class, 'show'])->whereNumber('ticket')->name('ticket.keluar.show');
Route::post('/keluar/check', [KeluarController::class, 'check']);
Route::post('/keluar/pay', [KeluarController::class, 'pay']);

Route::get('/transaksi', [TransaksiController::class, 'index'])->name('ticket.index');

Route::get('/area', [AreaController::class, 'index'])->name('ticket.area');
Route::get('/area/create', [AreaController::class, 'create'])->name('ticket.area.create');
Route::post('/area', [AreaController::class, 'store']);
Route::get('/area/{area}/edit', [AreaController::class, 'edit'])->name('ticket.area.edit');
Route::put('/area/{area}', [AreaController::class, 'update']);
Route::delete('/area/{area}', [AreaController::class, 'destroy']);

Route::get('/kendaraan', [KendaraanController::class, 'index'])->name('ticket.kendaraan');
Route::get('/kendaraan/create', [KendaraanController::class, 'create'])->name('ticket.kendaraan.create');
Route::post('/kendaraan', [KendaraanController::class, 'store']);
Route::get('/kendaraan/{kendaraan}/edit', [KendaraanController::class, 'edit'])->name('ticket.kendaraan.edit');
Route::put('/kendaraan/{kendaraan}', [KendaraanController::class, 'update']);
Route::delete('/kendaraan/{kendaraan}', [KendaraanController::class, 'destroy']);

Route::get('/log', [LogController::class, 'index'])->name('log');
Route::get('/log-aktivitas', [LogController::class, 'index']);
