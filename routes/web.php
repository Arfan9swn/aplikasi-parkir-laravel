<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('beranda');
});

Route::get('/registrasi', function () {
    return view('auth.registrasi');
});

Route::get('/masuk', function () {
    return view('auth.masuk');
});

Route::get('/dashboard', function () {
    return view('dashboard.dashboard');
});

Route::get('/riwayat-parkir', function () {
    return view('dashboard.riwayat_parkir');
});

Route::get('/transaksi', function () {
    return view('dashboard.transaksi');
});

Route::get('/log-aktivitas', function () {
    return view('dashboard.log_aktivitas');
});

Route::get('/area', function () {
    return view('dashboard.area');
});

Route::get('/kendaraan', function () {
    return view('dashboard.kendaraan');
});