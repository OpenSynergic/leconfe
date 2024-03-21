<?php

namespace App\Providers;

use App\Frontend\Conference\Blocks\CalendarBlock;
use App\Frontend\Conference\Blocks\CommitteeBlock;
use App\Frontend\Conference\Blocks\PreviousBlock;
use App\Frontend\Conference\Blocks\SubmitBlock;
use App\Frontend\Conference\Blocks\TimelineBlock;
use App\Frontend\Conference\Blocks\TopicBlock;
use App\Facades\Block;
use App\Facades\Plugin;
use App\Http\Middleware\MustVerifyEmail;
use App\Http\Middleware\Panel\PanelAuthenticate;
use App\Models\Enums\ConferenceStatus;
use App\Panel\Conference\Pages\Dashboard;
use App\Panel\Conference\Resources\UserResource;
use Filament\Actions\Action;
use Filament\Actions\MountableAction;
use Filament\Facades\Filament;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TimePicker;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Panel;
use Filament\Support\Colors\Color;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class PanelProvider extends ServiceProvider
{
    public function conferencePanel(Panel $panel): Panel
    {
        $this->setupPanel($panel)
            ->id('conference')
            ->default()
            ->path('{conference:path}/panel')
            ->bootUsing(fn() => static::setupFilamentComponent())
            ->homeUrl(fn() => route('livewirePageGroup.conference.pages.home', ['conference' => app()->getCurrentConference()]))
            ->discoverResources(in: app_path('Panel/Conference/Resources'), for: 'App\\Panel\\Conference\\Resources')
            ->discoverPages(in: app_path('Panel/Conference/Pages'), for: 'App\\Panel\\Conference\\Pages')
            ->discoverWidgets(in: app_path('Panel/Conference/Widgets'), for: 'App\\Panel\\Conference\\Widgets')
            ->discoverLivewireComponents(in: app_path('Panel/Conference/Livewire'), for: 'App\\Panel\\Conference\\Livewire')
            ->pages(static::getPages())
            ->userMenuItems(static::getUserMenuItems())
            ->renderHook(
                'panels::topbar.start',
                fn () => view('panel.conference.hooks.topbar'),
            );

        Plugin::getPlugins()->each(function ($plugin) use ($panel) {
            $plugin->onConferencePanel($panel);
        });

        return $panel;
    }

    public function administrationPanel(Panel $panel): Panel
    {
        $this->setupPanel($panel)
            ->id('administration')
            ->path('administration')
            ->homeUrl(fn() => route('livewirePageGroup.website.pages.home'))
            ->bootUsing(function(){
                static::setupFilamentComponent();
            })
            ->discoverResources(in: app_path('Panel/Administration/Resources'), for: 'App\\Panel\\Administration\\Resources')
            ->discoverPages(in: app_path('Panel/Administration/Pages'), for: 'App\\Panel\\Administration\\Pages')
            ->discoverWidgets(in: app_path('Panel/Administration/Widgets'), for: 'App\\Panel\\Administration\\Widgets')
            ->discoverLivewireComponents(in: app_path('Panel/Administration/Livewire'), for: 'App\\Panel\\Administration\\Livewire')
            ->renderHook(
                'panels::topbar.start',
                fn () => view('panel.administration.hooks.topbar'),
            );

        Plugin::getPlugins()->each(function ($plugin) use ($panel) {
            $plugin->onAdministrationPanel($panel);
        });

        return $panel;
    }

    public function setupPanel(Panel $panel): Panel
    {
        return $panel
            ->sidebarCollapsibleOnDesktop()
            ->maxContentWidth('full')
            ->renderHook(
                'panels::scripts.before',
                fn () => Blade::render(<<<'Blade'
                        @vite(['resources/panel/js/panel.js'])
                    Blade)
            )
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->viteTheme('resources/panel/css/panel.css')
            ->colors([
                'primary' => Color::hex('#09b8ed'),
            ])
            ->darkMode(false)
            ->databaseNotifications()
            ->middleware(static::getMiddleware(), true)
            ->authMiddleware(static::getAuthMiddleware(), true);
    }

    public function register(): void
    {
        Filament::registerPanel(
            fn (): Panel => $this->conferencePanel(Panel::make()),
        );

        Filament::registerPanel(
            fn (): Panel => $this->administrationPanel(Panel::make()),
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::anonymousComponentPath(resource_path('views/panel/conference/components'), 'panel');
        Blade::anonymousComponentPath(resource_path('views/panel/administration/components'), 'administration');
    }

    public static function getPages(): array
    {
        return [
            Dashboard::class,
        ];
    }

    public static function getWidgets(): array
    {
        return [];
    }

    public static function getMiddleware(): array
    {
        return [
            'web',
            DisableBladeIconComponents::class,
            DispatchServingFilamentEvent::class,
            'logout.banned',
        ];
    }

    public static function getAuthMiddleware(): array
    {
        return [
            PanelAuthenticate::class,
            MustVerifyEmail::class,
        ];
    }

    public static function setupFilamentComponent()
    {
        // TODO Validasi file type menggunakan format extension, bukan dengan mime type,
        // hal ini agar mempermudah pengguna dalam melakukan setting file apa saja yang diperbolehkan
        // Saat ini SpatieMediaLibraryFileUpload hanya support file validation dengan mime type.
        // Solusi mungkin buat custom component upload dengan menggunakan library seperti dropzone, atau yang lainnya.
        SpatieMediaLibraryFileUpload::configureUsing(function (SpatieMediaLibraryFileUpload $fileUpload): void {
            $fileUpload
                ->imageResizeTargetWidth(2048)
                ->imageResizeTargetWidth(2048)
                ->imageResizeMode('contain')
                ->imageResizeUpscale(false)
                ->maxSize(config('media-library.max_file_size') / 1024);

            // ->acceptedFileTypes(config('media-library.accepted_file_types'))
        });
        DatePicker::configureUsing(function (DatePicker $datePicker): void {
            $datePicker
                ->native(false)
                ->format(setting('format.date'));
        });

        TimePicker::configureUsing(function (TimePicker $timePicker): void {
            $timePicker->format(setting('format.time'));
        });

        Table::configureUsing(function (Table $table): void {
            $table
                ->defaultPaginationPageOption(5)
                ->paginationPageOptions([5, 10, 25, 50]);
        });

        TinyEditor::configureUsing(function (TinyEditor $tinyEditor): void {
            $tinyEditor
                ->setRelativeUrls(false)
                ->setRemoveScriptHost(false)
                ->toolbarSticky(false);
        });
    }

    public static function getUserMenuItems()
    {
        return [
            'logout' => MenuItem::make()
                ->url(fn (): string => route('filament.conference.auth.logout', ['conference' => app()->getCurrentConference()])),
            'profile' => MenuItem::make()
                ->url(fn (): string => UserResource::getUrl('profile')),
        ];
    }
}