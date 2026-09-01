<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $config = null;
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('configuracions')) {
                $config = \App\Models\Configuracion::first();
            }
        } catch (\Exception $e) {
            // Ignore if DB not ready
        }

        $hexColor = $config && $config->color_principal ? $config->color_principal : '#c71b04';
        
        // Generar paleta completa pero forzar que los tonos principales (botones) sean el color exacto
        $primaryColor = \Filament\Support\Colors\Color::hex($hexColor);
        $primaryColor[500] = \Filament\Support\Colors\Color::hex($hexColor)[500]; // Mantener compatibilidad si es necesario
        // Truco: Forzamos el color exacto en los tonos que Filament usa para botones
        $primaryColor[500] = $hexColor; 
        $primaryColor[600] = $hexColor;

        if ($config) {
            if ($config->nombre) $panel->brandName($config->nombre);
            
            if ($config->logo_url) {
                $panel->brandLogo($config->logo_url);
                $panel->brandLogoHeight('3.5rem'); // Aumentar tamaño
            } elseif ($config->logo) {
                $panel->brandLogo(asset('storage/' . $config->logo));
                $panel->brandLogoHeight('3.5rem'); // Aumentar tamaño
            }
            
            if ($config->favicon) $panel->favicon(asset('storage/' . $config->favicon));
        }

        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->globalSearch(false)
            ->renderHook(
                \Filament\View\PanelsRenderHook::USER_MENU_BEFORE,
                fn (): string => view('filament.hooks.header-clock'),
            )
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->renderHook(
                \Filament\View\PanelsRenderHook::HEAD_END,
                fn (): string => \Illuminate\Support\Facades\Blade::render('
                    <style>
                        /* Dark Sidebar Theme Override */
                        .fi-sidebar {
                            background-color: #111111 !important; /* Negro Institucional */
                            border-right: 1px solid rgba(255, 255, 255, 0.05) !important;
                        }
                        
                        /* Scrollbar del Menú Lateral */
                        .fi-sidebar-nav {
                            scrollbar-width: thin;
                            scrollbar-color: #333333 #111111;
                        }
                        .fi-sidebar-nav::-webkit-scrollbar {
                            width: 6px;
                        }
                        .fi-sidebar-nav::-webkit-scrollbar-track {
                            background: #111111;
                        }
                        .fi-sidebar-nav::-webkit-scrollbar-thumb {
                            background-color: #333333;
                            border-radius: 10px;
                        }
                        .fi-sidebar-nav::-webkit-scrollbar-thumb:hover {
                            background-color: #555555;
                        }
                        
                        /* Enlaces del Sidebar (Inactivos) */
                        .fi-sidebar-item-btn,
                        .fi-sidebar-item-label {
                            color: #ffffff !important;
                            font-weight: 500;
                            transition: all 0.3s ease;
                        }
                        .fi-sidebar-item-btn:hover {
                            background-color: rgba(255, 255, 255, 0.1) !important;
                        }
                        
                        /* Elemento Activo (Color Dinámico) */
                        .fi-sidebar-item.fi-active > .fi-sidebar-item-btn,
                        .fi-active > a {
                            background-color: {{ $color }} !important;
                            color: #ffffff !important;
                            font-weight: 700 !important;
                        }
                        .fi-sidebar-item.fi-active .fi-sidebar-item-icon {
                            color: #ffffff !important;
                        }

                        /* Iconos inactivos */
                        .fi-sidebar-item-icon {
                            color: #ffffff !important;
                            opacity: 0.7;
                        }
                        .fi-sidebar-item-btn:hover .fi-sidebar-item-icon {
                            opacity: 1;
                        }
                        
                        /* Títulos de Grupos (Configuración Global, etc) */
                        .fi-sidebar-group-label {
                            color: #a3a3a3 !important;
                            text-transform: uppercase;
                            letter-spacing: 0.05em;
                            font-weight: 700 !important;
                        }

                        /* Topbar Mejorado (Blanco) */
                        .fi-topbar {
                            background-color: #ffffff !important;
                            border-top: 4px solid {{ $color }} !important;
                            border-bottom: 2px solid {{ $color }} !important;
                            box-shadow: 0 4px 15px -3px rgba(0, 0, 0, 0.05) !important;
                        }
                        
                        /* Dashboard profesional */
                        .fi-dashboard-page {
                            background: linear-gradient(180deg, #f8fafc 0%, #eef2ff 100%);
                            padding-top: 1rem;
                        }
                        .fi-dashboard-page .fi-section {
                            background: rgba(255,255,255,0.84) !important;
                            backdrop-filter: blur(6px);
                            border: 1px solid rgba(148,163,184,0.18) !important;
                            border-radius: 1rem !important;
                            box-shadow: 0 18px 35px -26px rgba(15,23,42,0.35) !important;
                        }
                        .fi-wi-stats-overview-widget .fi-stat {
                            background: linear-gradient(135deg, rgba(255,255,255,0.95), rgba(248,250,252,0.9)) !important;
                            border: 1px solid rgba(148,163,184,0.2) !important;
                            border-radius: 1rem !important;
                            box-shadow: 0 10px 25px -18px rgba(15,23,42,0.35) !important;
                        }
                        .fi-wi-chart-widget .fi-section {
                            border-radius: 1.1rem !important;
                        }
                        .fi-wi-chart-widget canvas {
                            max-height: 260px !important;
                        }
                        /* Soporte para Logo en Barra Lateral Oscura (Móviles) */
                        .fi-sidebar-header {
                            background-color: #ffffff !important;
                            border-bottom: 2px solid {{ $color }} !important;
                        }
                        .fi-sidebar-header .fi-logo {
                            padding: 0.5rem 0;
                        }
                    </style>
                ', ['color' => $config && $config->color_principal ? $config->color_principal : '#c71b04'])
            )
            ->login()
            ->colors([
                'primary' => $primaryColor,
            ])
            ->font('Montserrat')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                \App\Filament\Widgets\ResumenGeneralWidget::class,
                \App\Filament\Widgets\CarrerasPorPeriodoWidget::class,
                #AccountWidget::class,
                #FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->plugins([
                FilamentShieldPlugin::make(),
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
