<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\BusinessSetting;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        try {
            View::share('business', BusinessSetting::first());
        } catch (\Throwable $e) {
            // Abaikan error saat migrasi awal belum selesai dilakukan
        }
    }
}
