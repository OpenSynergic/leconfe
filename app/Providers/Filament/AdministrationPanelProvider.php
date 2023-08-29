<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\PanelProvider as FilamentPanelProvider;


class AdministrationPanelProvider extends FilamentPanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('administration')
            ->path('administration')
            ->homeUrl(fn () => route('livewirePageGroup.website.pages.home'))
            ->colors(PanelProvider::getColors())
            ->renderHook(
                'panels::sidebar.nav.start',
                fn () => view('administration.components.sidebar.nav-start')
            )
            ->discoverResources(in: app_path('Administration/Resources'), for: 'App\\Administration\\Resources')
            ->discoverPages(in: app_path('Administration/Pages'), for: 'App\\Administration\\Pages')
            ->discoverWidgets(in: app_path('Administration/Widgets'), for: 'App\\Administration\\Widgets')
            ->viteTheme('resources/panel/css/panel.css')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware(PanelProvider::getMiddleware())
            ->authMiddleware(PanelProvider::getAuthMiddleware());
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::anonymousComponentPath(resource_path('views/administration/components'), 'administration');
    }
}
