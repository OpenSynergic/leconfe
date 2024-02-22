<?php

namespace App\Providers\Filament;

use App\Conference\Blocks\CalendarBlock;
use App\Conference\Blocks\CommitteeBlock;
use App\Conference\Blocks\InformationBlock;
use App\Conference\Blocks\PreviousBlock;
use App\Conference\Blocks\SubmitBlock;
use App\Conference\Blocks\TimelineBlock;
use App\Conference\Blocks\TopicBlock;
use App\Facades\Block;
use App\Facades\Plugin;
use App\Http\Middleware\MustVerifyEmail;
use App\Http\Middleware\Panel\PanelAuthenticate;
use App\Http\Middleware\Panel\TenantConferenceMiddleware;
use App\Models\Conference;
use App\Models\Enums\ConferenceStatus;
use App\Models\Navigation;
use App\Models\Site;
use App\Panel\Resources\NavigationResource;
use App\Panel\Resources\UserResource;
use Carbon\Carbon;
use Coolsam\FilamentFlatpickr\Forms\Components\Flatpickr;
use Filament\Actions\Action;
use Filament\Actions\MountableAction;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TimePicker;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationGroup;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider as FilamentPanelProvider;
use Filament\Support\Colors\Color;
use Filament\Tables\Table;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Blade;
use Livewire\Livewire;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;
use RyanChandler\FilamentNavigation\FilamentNavigation;

class PanelProvider extends FilamentPanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $panel
            ->default()
            ->sidebarCollapsibleOnDesktop()
            ->id('panel')
            ->path(config('app.filament.panel_path'))
            ->maxContentWidth('full')
            // ->spa()
            ->homeUrl(fn () => App::isInstalled() ? App::getCurrentConference()->getHomeUrl() : null)
            ->bootUsing(fn ($panel) => $this->panelBootUsing($panel))
            ->tenantMenu(false)
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
            ->renderHook(
                'panels::topbar.start',
                fn () => view('panel.hooks.topbar'),
            )
            ->viteTheme('resources/panel/css/panel.css')
            ->tenant(Conference::class, 'path')
            ->tenantMiddleware(static::getTenantMiddleware(), true)
            ->tenantMenuItems([
                MenuItem::make()
                    ->label('Administration')
                    ->url(fn (): string => url('administration'))
                    // ->url(fn (): string => route('filament.administration.pages.dashboard'))
                    ->icon('heroicon-m-cog-8-tooth')
                    ->hidden(fn () => ! auth()->user()->can('view', Site::class)),
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
            ->userMenuItems(static::getUserMenuItems())
            ->plugins(static::getPlugins());

        Plugin::getPlugins()->each(function ($plugin) use ($panel) {
            $plugin->onPanel($panel);
        });

        return $panel;
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::anonymousComponentPath(resource_path('views/panel/components'), 'panel');

        // Persistent middleware option on filament doesnt work, currently we use this workaround
        Livewire::addPersistentMiddleware(static::getTenantMiddleware());
    }

    public function panelBootUsing(Panel $panel): void
    {
        static::setupFilamentComponent();

        // Disable form when Conference status is archived
        ComponentContainer::configureUsing(function (ComponentContainer $componentContainer): void {
            if (App::getCurrentConference()->status == ConferenceStatus::Archived) {
                $componentContainer->disabled(true);
            }
        });

        // Disable action when Conference status is archived
        MountableAction::configureUsing(function (MountableAction $action): void {
            if (App::getCurrentConference()->status == ConferenceStatus::Archived) {
                $action->disabled(true);
            }
        });

        Block::registerBlocks([
            new CalendarBlock,
            new TimelineBlock,
            new PreviousBlock,
            new SubmitBlock,
            new TopicBlock,
            new CommitteeBlock,
            // InformationBlock::class,
        ]);
        Block::boot();
    }

    public static function getTenantMiddleware(): array
    {
        return [
            // TenantConferenceMiddleware::class,
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
        // TODO Validasi file type menggunakan dengan menggunakan format extension, bukan dengan mime type,
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

    public static function getPlugins()
    {
        return [
            FilamentNavigation::make()
                ->usingModel(Navigation::class)
                ->usingResource(NavigationResource::class)
                ->itemType('Home', [])
                ->itemType('About', [])
                ->itemType('Announcements', [])
                ->itemType('Current Conference', [])
                ->itemType('Login', [])
                ->itemType('Register', [])
                ->itemType('Proceeding', []),
        ];
    }

    public static function getUserMenuItems()
    {
        return [
            'profile' => MenuItem::make()
                ->url(fn (): string => UserResource::getUrl('profile')),
        ];
    }
}
