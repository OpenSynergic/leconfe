<?php

namespace App\Providers\Filament;

use App\Http\Middleware\ApplyTenantScopes;
use App\Models\Conference;
use App\Models\Navigation;
use App\Panel\Resources\NavigationResource;
use Carbon\Carbon;
use Coolsam\FilamentFlatpickr\Forms\Components\Flatpickr;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TimePicker;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationGroup;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider as FilamentPanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use RyanChandler\FilamentNavigation\FilamentNavigation;

class PanelProvider extends FilamentPanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('panel')
            ->path(config('app.filament.panel_path'))
            ->maxContentWidth('full')
            ->homeUrl(fn () => route('livewirePageGroup.website.pages.home'))
            ->bootUsing(fn () => static::setupFilamentComponent())
            // ->renderHook(
            //     'panels::sidebar.footer',
            //     fn () => view('panel.components.sidebar.footer')
            // )
            ->renderHook(
                'panels::scripts.before',
                fn () => Blade::render(<<<'Blade'
                        @vite(['resources/panel/js/panel.js'])
                    Blade)
            )
            ->viteTheme('resources/panel/css/panel.css')
            ->tenant(Conference::class)
            ->tenantMiddleware(static::getTenantMiddleware(), true)
            ->tenantMenuItems([
                MenuItem::make()
                    ->label('Administration')
                    ->url(fn (): string => url('administration'))
                    // ->url(fn (): string => route('filament.administration.pages.dashboard'))
                    ->icon('heroicon-m-cog-8-tooth'),
            ])
            ->navigationGroups(static::getNavigationGroups())
            ->navigationItems(static::getNavigationItems())
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->colors(static::getColors())
            ->discoverResources(in: app_path('Panel/Resources'), for: 'App\\Panel\\Resources')
            ->discoverPages(in: app_path('Panel/Pages'), for: 'App\\Panel\\Pages')
            ->discoverWidgets(in: app_path('Panel/Widgets'), for: 'App\\Panel\\Widgets')
            ->discoverLivewireComponents(in: app_path('Panel/Livewire'), for: 'App\\Panel\\Livewire')
            ->pages(static::getPages())
            ->widgets(static::getWidgets())
            ->databaseNotifications()
            ->databaseNotificationsPolling('120s')
            ->middleware(static::getMiddleware(), true)
            ->authMiddleware(static::getAuthMiddleware(), true)
            ->plugin(
                FilamentNavigation::make()
                    ->usingModel(Navigation::class)
                    ->usingResource(NavigationResource::class)
            );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::anonymousComponentPath(resource_path('views/panel/components'), 'panel');
        Blade::anonymousComponentPath(resource_path('views/forms/components'), 'form');
    }

    public static function getTenantMiddleware(): array
    {
        return [
            ApplyTenantScopes::class,
        ];
    }

    public static function getNavigationGroups(): array
    {
        return [
            NavigationGroup::make()
                ->label('Settings'),
            NavigationGroup::make()
                ->label('Administration'),
        ];
    }

    public static function getNavigationItems(): array
    {
        return [];
    }

    public static function getColors(): array
    {
        return [
            'primary' => Color::hex('#09b8ed'),
        ];
    }

    public static function getPages(): array
    {
        return [
            Pages\Dashboard::class,
        ];
    }

    public static function getWidgets(): array
    {
        return [];
    }

    public static function getMiddleware(): array
    {
        return [
            // EncryptCookies::class,
            // AddQueuedCookiesToResponse::class,
            // StartSession::class,
            // AuthenticateSession::class,
            // ShareErrorsFromSession::class,
            // VerifyCsrfToken::class,
            // SubstituteBindings::class,
            'web',
            DisableBladeIconComponents::class,
            DispatchServingFilamentEvent::class,
        ];
    }

    public static function getAuthMiddleware(): array
    {
        return [
            Authenticate::class,
        ];
    }

    public static function setupFilamentComponent()
    {
        // TODO Validasi file type menggunakan dengan menggunakan format extension, bukan dengan mime type, hal ini agar mempermudah pengguna dalam melakukan setting file apa saja yang diperbolehkan
        // Saat ini SpatieMediaLibraryFileUpload hanya support file validation dengan mime type.
        // Solusi mungkin buat custom component upload dengan menggunakan library seperti dropzone, atau yang lainnya.
        SpatieMediaLibraryFileUpload::configureUsing(function (SpatieMediaLibraryFileUpload $fileUpload): void {
            $fileUpload
                ->imageResizeTargetWidth(1024)
                ->imageResizeUpscale(false)
                ->maxSize(config('media-library.max_file_size') / 1024);

            // ->acceptedFileTypes(config('media-library.accepted_file_types'))
        });
        DatePicker::configureUsing(function (DatePicker $datePicker): void {
            $datePicker->format(setting('format.date'));
        });

        TimePicker::configureUsing(function (TimePicker $timePicker): void {
            $timePicker->format(setting('format.time'));
        });

        Flatpickr::configureUsing(function (Flatpickr $flatpickr): void {
            $flatpickr
                ->dateFormat(setting('format.date'))
                ->dehydrateStateUsing(fn ($state) => $state ? Carbon::createFromFormat(setting('format.date'), $state) : null);
        });
    }
}
