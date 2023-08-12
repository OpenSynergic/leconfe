<?php

namespace App\Providers\Filament;

use App\Http\Middleware\ApplyTenantScopes;
use App\Models\Conference;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider as FilamentPanelProvider;
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

class PanelProvider extends FilamentPanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->login()
            ->default()
            ->id('panel')
            ->path('panel')
            ->maxContentWidth('full')
            ->renderHook(
                'panels::scripts.before',
                fn () => Blade::render(<<<'Blade'
                        @vite(['resources/panel/js/panel.js'])
                    Blade)
            )
            ->viteTheme('resources/panel/css/panel.css')
            ->tenant(Conference::class)
            ->tenantMiddleware($this->getTenantMiddleware(), true)
            ->navigationGroups($this->getNavigationGroups())
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->registration()
            ->passwordReset()
            ->emailVerification()
            ->profile()
            ->colors($this->getColors())
            ->discoverResources(in: app_path('Panel/Resources'), for: 'App\\Panel\\Resources')
            ->discoverPages(in: app_path('Panel/Pages'), for: 'App\\Panel\\Pages')
            ->discoverWidgets(in: app_path('Panel/Widgets'), for: 'App\\Panel\\Widgets')
            ->pages($this->getPages())
            ->widgets($this->getWidgets())
            ->databaseNotifications()
            ->databaseNotificationsPolling('120s')
            ->middleware($this->getMiddleware(), true)
            ->authMiddleware($this->getAuthMiddleware(), true);
    }

    protected function getTenantMiddleware(): array
    {
        return [
            ApplyTenantScopes::class,
        ];
    }

    protected function getNavigationGroups(): array
    {
        return [
            NavigationGroup::make()
                ->label('Settings'),
            NavigationGroup::make()
                ->label('Administration'),
        ];
    }

    protected function getColors(): array
    {
        return [
            'primary' => Color::Cyan,
        ];
    }

    protected function getPages(): array
    {
        return [
            Pages\Dashboard::class,
        ];
    }

    protected function getWidgets(): array
    {
        return [
            Widgets\AccountWidget::class,
            Widgets\FilamentInfoWidget::class,
        ];
    }

    protected function getMiddleware(): array
    {
        return [
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            AuthenticateSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
            SubstituteBindings::class,
            DisableBladeIconComponents::class,
            DispatchServingFilamentEvent::class,
        ];
    }

    protected function getAuthMiddleware(): array
    {
        return [
            Authenticate::class,
        ];
    }
}
