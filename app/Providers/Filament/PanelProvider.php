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
            ->login()
            ->default()
            ->id('panel')
            ->path('panel')
            ->maxContentWidth('full')
            ->homeUrl(fn () => route('livewirePageGroup.website.pages.home'))
            ->bootUsing(fn () => $this->setupFilamentComponent())
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
            ->discoverLivewireComponents(in: app_path('Panel/Livewire'), for: 'App\\Panel\\Livewire')
            ->pages($this->getPages())
            ->widgets($this->getWidgets())
            ->databaseNotifications()
            ->databaseNotificationsPolling('120s')
            ->middleware($this->getMiddleware(), true)
            ->authMiddleware($this->getAuthMiddleware(), true)
            ->plugin(
                FilamentNavigation::make()
                    ->usingModel(Navigation::class)
                    ->usingResource(NavigationResource::class)
            );
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
        return [];
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

    protected function setupFilamentComponent()
    {
        // TODO Validasi file type menggunakan dengan menggunakan format extension, bukan dengan mime type, hal ini agar mempermudah pengguna dalam melakukan setting file apa saja yang diperbolehkan
        // Saat ini SpatieMediaLibraryFileUpload hanya support file validation dengan mime type.
        // Solusi mungkin buat custom component upload dengan menggunakan library seperti dropzone, atau yang lainnya.
        SpatieMediaLibraryFileUpload::configureUsing(function (SpatieMediaLibraryFileUpload $fileUpload): void {
            $fileUpload->maxSize(config('media-library.max_file_size') / 1024);
            // ->acceptedFileTypes(config('media-library.accepted_file_types'))
        });
        DatePicker::configureUsing(function (DatePicker $datePicker): void {
            $datePicker->format(setting('format.date'));
        });

        TimePicker::configureUsing(function (TimePicker $timePicker): void {
            $timePicker->format(setting('format.time'));
        });

        Flatpickr::configureUsing(function (Flatpickr $flatpickr): void {
            // $flatpickr
            //     ->dateFormat(setting('format.date'));
            //     ->dehydrateStateUsing(fn($state) => dd(Carbon::createFromFormat(setting('format.date'), $state)));
        });
    }
}
