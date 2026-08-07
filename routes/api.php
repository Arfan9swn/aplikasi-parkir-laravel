<?php
use App\Http\Controllers\Api\AreasController;
use App\Http\Controllers\Api\KendaraansController;
use App\Http\Controllers\Api\LogsController;
use App\Http\Controllers\Api\TarifsController;
use App\Http\Controllers\Api\TransaksisController;
use App\Http\Controllers\Api\UsersController;


Route::apiResources([
    'areas'    => AreasController::class,
    'kendaraans' => KendaraansController::class,
    'logs' => LogsController::class,
    'tarifs' => TarifsController::class,
    'transaksis' => TransaksisController::class,
    'users' => UsersController::class
]);
?>