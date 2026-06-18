<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PlaceholderController;
use App\Http\Controllers\AccountController;

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Rute Staff & Admin
    Route::middleware('role:admin,staff')->group(function () {
        Route::get('/kendaraan-masuk', [PlaceholderController::class, 'index'])->name('kendaraan-masuk');
        Route::get('/cari-kendaraan', [PlaceholderController::class, 'index'])->name('cari-kendaraan');
        Route::get('/kendaraan-keluar', [PlaceholderController::class, 'index'])->name('kendaraan-keluar');
        Route::get('/manajemen-akun', [AccountController::class, 'index'])->name('manajemen-akun');
    });

    // Rute Khusus Admin
    Route::middleware('role:admin')->group(function () {
        Route::get('/pengaturan-tarif', [PlaceholderController::class, 'index'])->name('pengaturan-tarif');
        Route::get('/laporan', [PlaceholderController::class, 'index'])->name('laporan');
        Route::get('/pengaturan-bisnis', [PlaceholderController::class, 'index'])->name('pengaturan-bisnis');
    });
});
