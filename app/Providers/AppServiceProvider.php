<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Illuminate\Support\ServiceProvider;

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
        Filament::getDefaultPanel()->navigationGroups([
            'Gestión Académica',
            'Infraestructura',
            'Configuración del Sistema',
            'Filament Shield',
            'Configuración Global',
        ]);
    }
}
