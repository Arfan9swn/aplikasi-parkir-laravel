<?php
use App\Http\Controllers\Api\AreasController;
use App\Http\Controllers\Api\KendaraansController;
use App\Http\Controllers\Api\LogsController;
use App\Http\Controllers\Api\TarifsController;
use App\Http\Controllers\Api\TransaksisController;
use App\Http\Controllers\Api\UsersController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Authentication — session-based, consumed by the ParkEase front-end
|--------------------------------------------------------------------------
*/
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/me', [AuthController::class, 'me']);
Route::post('/logout', [AuthController::class, 'logout']);

/*
|--------------------------------------------------------------------------
| Area parkir — view is public; add/update/delete only for admin & petugas
|--------------------------------------------------------------------------
*/
Route::apiResource('areas', AreasController::class)->only(['index', 'show']);

Route::middleware('role:admin,petugas')->group(function () {
    Route::apiResource('areas', AreasController::class)->only(['store', 'update', 'destroy']);
});

Route::apiResources([
    'kendaraans' => KendaraansController::class,
    'logs' => LogsController::class,
    'tarifs' => TarifsController::class,
    'transaksis' => TransaksisController::class,
    'users' => UsersController::class
]);

/*
|--------------------------------------------------------------------------
| Log monitoring — reading the raw log files is restricted to staff roles.
|--------------------------------------------------------------------------
*/
Route::get('/system-logs', [LogsController::class, 'systemLog'])
    ->middleware('role:admin,petugas,owner');
?>