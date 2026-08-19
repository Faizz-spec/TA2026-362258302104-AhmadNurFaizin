<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Foundation\MaintenanceModeManager;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(
            'Illuminate\Contracts\Foundation\MaintenanceMode',
            fn ($app) => $app->make(MaintenanceModeManager::class)
        );
    }

    public function boot(): void
    {
        if (str_starts_with(config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }
    }
}