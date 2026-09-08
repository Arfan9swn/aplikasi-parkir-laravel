<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TicketController;

/*
|--------------------------------------------------------------------------
| ParkEase — simple parking ticketing
|--------------------------------------------------------------------------
| Pages render here; all dynamic data is fetched client-side from /api/*.
*/

Route::get('/', function () {
    return view('beranda');
})->name('beranda');

Route::get('/masuk', [TicketController::class, 'ticketMasuk'])->name('ticket.masuk');
Route::get('/keluar', [TicketController::class, 'ticketKeluar'])->name('ticket.keluar');
Route::get('/transaksi', [TicketController::class, 'ticketIndex'])->name('ticket.index');
Route::get('/area', [TicketController::class, 'ticketArea'])->name('ticket.area');
Route::get('/kendaraan', [TicketController::class, 'ticketKendaraan'])->name('ticket.kendaraan');

/*
|--------------------------------------------------------------------------
| Legacy pages (previous dashboard & auth views)
|--------------------------------------------------------------------------
*/

Route::get('/login', function () {
    if (session('auth_user')) {
        return redirect()->route('beranda');
    }
    return view('auth.login');
})->name('login');

Route::post('/logout', [AuthController::class, 'logoutWeb'])->name('logout');

Route::get('/registrasi', function () {
    return view('auth.registrasi');
});

Route::get('/dashboard', function () {
    return view('dashboard.dashboard');
});

Route::get('/riwayat-parkir', function () {
    return view('dashboard.riwayat.index');
});

Route::get('/log-aktivitas', function () {
    return view('dashboard.aktivitas.index');
});
