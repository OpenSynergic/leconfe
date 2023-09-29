<?php

namespace App\Providers\Filament;

use App\Administration\Resources\NavigationResource;
use App\Models\Navigation;
use Filament\Panel;
use Filament\PanelProvider as FilamentPanelProvider;
use Filament\Widgets;
use Illuminate\Support\Facades\Blade;
use RyanChandler\FilamentNavigation\FilamentNavigation;

class AdministrationPanelProvider extends FilamentPanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('administration')
            ->plugins($this->getPlugins())
            ->path(config('app.filament.administration_path'))
            ->homeUrl(fn () => route('livewirePageGroup.website.pages.home'))
            ->colors(PanelProvider::getColors())
            ->bootUsing(fn () => $this->bootUsing())
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

    public function getPlugins(): array
    {
        return [
            FilamentNavigation::make()
                ->usingModel(Navigation::class)
                ->usingResource(NavigationResource::class)
                ->itemType('Home', [])
                ->itemType('Current Conference', [])
                ->itemType('Login', [])
                ->itemType('Register', []),
        ];
    }

    public function bootUsing()
    {
        app()->scopeCurrentConference();

        PanelProvider::setupFilamentComponent();
    }
}
