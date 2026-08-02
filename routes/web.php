<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PlaceholderController;
use App\Http\Controllers\AccountController;

use App\Http\Controllers\TariffController;
use App\Http\Controllers\EntryController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ExitController;

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // Password Reset Routes
    Route::get('/forgot-password', [\App\Http\Controllers\Auth\PasswordResetController::class, 'showForgotForm'])->name('password.request');
    Route::post('/forgot-password', [\App\Http\Controllers\Auth\PasswordResetController::class, 'sendResetCode'])->name('password.email');
    Route::get('/reset-password', [\App\Http\Controllers\Auth\PasswordResetController::class, 'showResetForm'])->name('password.reset.form');
    Route::post('/reset-password', [\App\Http\Controllers\Auth\PasswordResetController::class, 'resetPassword'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Rute Khusus Staff
    Route::middleware('role:staff')->group(function () {
        Route::get('/kendaraan-masuk', [EntryController::class, 'index'])->name('kendaraan-masuk');
        Route::post('/kendaraan-masuk/simpan', [EntryController::class, 'store'])->name('kendaraan-masuk.store');
        Route::get('/cari-kendaraan', [SearchController::class, 'index'])->name('cari-kendaraan');
        Route::get('/kendaraan-keluar', [ExitController::class, 'index'])->name('kendaraan-keluar');
        Route::get('/kendaraan-keluar/{id}/proses', [ExitController::class, 'process'])->name('kendaraan-keluar.process');
        Route::post('/kendaraan-keluar/{id}/checkout', [ExitController::class, 'checkout'])->name('kendaraan-keluar.checkout');
        
        // Edit & Hapus (Fitur Petugas)
        Route::put('/kendaraan/{id}', [EntryController::class, 'update'])->name('kendaraan.update');
        Route::delete('/kendaraan/{id}', [EntryController::class, 'destroy'])->name('kendaraan.destroy');
    });
        Route::get('/users', [\App\Http\Controllers\UserController::class, 'index'])->name('users.index');
        Route::post('/users', [\App\Http\Controllers\UserController::class, 'store'])->name('users.store');
        Route::put('/users/{id}', [\App\Http\Controllers\UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{id}', [\App\Http\Controllers\UserController::class, 'destroy'])->name('users.destroy');


    // Rute Khusus Admin
    Route::middleware('role:admin')->group(function () {

        Route::get('/pengaturan-tarif', [TariffController::class, 'index'])->name('pengaturan-tarif');
        Route::put('/pengaturan-tarif/update', [TariffController::class, 'update'])->name('pengaturan-tarif.update');
        
        // Report Routes
        Route::get('/laporan', [App\Http\Controllers\ReportController::class, 'index'])->name('laporan');
        Route::get('/laporan/export/pdf', [App\Http\Controllers\ReportController::class, 'exportPdf'])->name('laporan.pdf');
        Route::get('/laporan/export/excel', [App\Http\Controllers\ReportController::class, 'exportExcel'])->name('laporan.excel');

        Route::get('/pengaturan-bisnis', [\App\Http\Controllers\BusinessSettingController::class, 'index'])->name('pengaturan-bisnis');
        Route::put('/pengaturan-bisnis', [\App\Http\Controllers\BusinessSettingController::class, 'update'])->name('pengaturan-bisnis.update');
        Route::delete('/pengaturan-bisnis/logo', [\App\Http\Controllers\BusinessSettingController::class, 'removeLogo'])->name('pengaturan-bisnis.logo.destroy');
    });
});
