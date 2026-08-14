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
use App\Http\Controllers\VehicleTypeController;
use App\Http\Controllers\ParkingSlotController;
use App\Http\Controllers\PaymentCallbackController;

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

// Rute Callback & Redirect Midtrans (Dapat dipanggil dari server Midtrans & Browser via Ngrok)
Route::post('/payment/callback', [PaymentCallbackController::class, 'notification'])->name('midtrans.callback');
Route::post('/api/midtrans/callback', [PaymentCallbackController::class, 'notification']);
Route::get('/payment/finish', [PaymentCallbackController::class, 'finish'])->name('midtrans.finish');
Route::get('/payment/unfinish', [PaymentCallbackController::class, 'unfinish'])->name('midtrans.unfinish');
Route::get('/payment/error', [PaymentCallbackController::class, 'error'])->name('midtrans.error');

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Rute Kendaraan Masuk (Diakses Admin untuk pemantauan & Staff untuk pencatatan)
    Route::get('/kendaraan-masuk', [EntryController::class, 'index'])->name('kendaraan-masuk');

    // Rute Khusus Staff
    Route::middleware('role:staff')->group(function () {
        Route::post('/kendaraan-masuk/simpan', [EntryController::class, 'store'])->name('kendaraan-masuk.store');
        Route::get('/kendaraan-masuk/{id}/karcis-pdf', [EntryController::class, 'printTicketPdf'])->name('kendaraan.karcis.pdf');
        Route::get('/cari-kendaraan', [SearchController::class, 'index'])->name('cari-kendaraan');
        Route::get('/kendaraan-keluar', [ExitController::class, 'index'])->name('kendaraan-keluar');
        Route::get('/kendaraan-keluar/{id}/proses', [ExitController::class, 'process'])->name('kendaraan-keluar.process');
        Route::post('/kendaraan-keluar/{id}/checkout', [ExitController::class, 'checkout'])->name('kendaraan-keluar.checkout');
        Route::post('/kendaraan-keluar/{id}/cancel', [ExitController::class, 'cancel'])->name('kendaraan-keluar.cancel');
        Route::post('/kendaraan-keluar/{id}/snap-token', [ExitController::class, 'generateSnapToken'])->name('kendaraan-keluar.snap-token');
        Route::post('/kendaraan-keluar/{id}/sync-status', [ExitController::class, 'syncPaymentStatus'])->name('kendaraan-keluar.sync-status');
        Route::get('/kendaraan-keluar/{id}/struk-pdf', [ExitController::class, 'printReceiptPdf'])->name('kendaraan-keluar.struk-pdf');
    });
        Route::get('/users', [\App\Http\Controllers\UserController::class, 'index'])->name('users.index');
        Route::post('/users', [\App\Http\Controllers\UserController::class, 'store'])->name('users.store');
        Route::put('/users/{id}', [\App\Http\Controllers\UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{id}', [\App\Http\Controllers\UserController::class, 'destroy'])->name('users.destroy');
        Route::patch('/users/{id}/toggle-status', [\App\Http\Controllers\UserController::class, 'toggleStatus'])->name('users.toggle-status');


    // Rute Khusus Admin
    Route::middleware('role:admin')->group(function () {
        // Manajemen Jenis Motor (Tanpa Hapus Permanen)
        Route::resource('vehicle-types', VehicleTypeController::class)->except(['show', 'destroy']);
        Route::patch('/vehicle-types/{vehicle_type}/toggle-status', [VehicleTypeController::class, 'toggleStatus'])->name('vehicle-types.toggle-status');

        // Pengaturan Kapasitas Slot Global
        Route::get('/pengaturan-slot', [ParkingSlotController::class, 'index'])->name('pengaturan-slot');
        Route::put('/pengaturan-slot', [ParkingSlotController::class, 'update'])->name('pengaturan-slot.update');

        Route::get('/pengaturan-tarif', [TariffController::class, 'index'])->name('pengaturan-tarif');
        Route::get('/pengaturan-tarif/{tariff}/edit', [TariffController::class, 'edit'])->name('pengaturan-tarif.edit');
        Route::put('/pengaturan-tarif/{tariff}', [TariffController::class, 'update'])->name('pengaturan-tarif.update');
        
        // Report Routes
        Route::get('/laporan', [App\Http\Controllers\ReportController::class, 'index'])->name('laporan');
        Route::get('/laporan/export/pdf', [App\Http\Controllers\ReportController::class, 'exportPdf'])->name('laporan.pdf');
        Route::get('/laporan/export/excel', [App\Http\Controllers\ReportController::class, 'exportExcel'])->name('laporan.excel');

        Route::get('/pengaturan-bisnis', [\App\Http\Controllers\BusinessSettingController::class, 'index'])->name('pengaturan-bisnis');
        Route::put('/pengaturan-bisnis', [\App\Http\Controllers\BusinessSettingController::class, 'update'])->name('pengaturan-bisnis.update');
        Route::delete('/pengaturan-bisnis/logo', [\App\Http\Controllers\BusinessSettingController::class, 'removeLogo'])->name('pengaturan-bisnis.logo.destroy');
    });
});
