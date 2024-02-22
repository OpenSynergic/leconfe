<?php

namespace App\Providers\Filament;

use App\Administration\Resources\NavigationResource;
use App\Facades\Block;
use App\Facades\Plugin;
use App\Models\Navigation;
use App\Website\Blocks\CalendarBlock;
use App\Website\Blocks\LoginBlock;
use App\Website\Blocks\SearchBlock;
use App\Website\Blocks\TimelineBlock;
use App\Website\Blocks\TopicBlock;
use App\Website\Blocks\UpcomingConferenceBlock;
use Filament\Panel;
use Filament\PanelProvider as FilamentPanelProvider;
use Filament\Widgets;
use Illuminate\Support\Facades\Blade;
use RyanChandler\FilamentNavigation\FilamentNavigation;

class AdministrationPanelProvider extends FilamentPanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $panel
            ->id('administration')
            ->plugins($this->getPlugins())
            ->sidebarCollapsibleOnDesktop()
            ->path(config('app.filament.administration_path'))
            ->homeUrl(fn () => route('livewirePageGroup.website.pages.home'))
            ->colors(PanelProvider::getColors())
            ->bootUsing(fn () => $this->bootUsing())
            ->renderHook(
                'panels::topbar.start',
                fn () => view('administration.hooks.topbar'),
            )
            ->discoverLivewireComponents(in: app_path('Administration/Livewire'), for: 'App\\Administration\\Livewire')
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
                ->itemType('Register', [])
                ->itemType('Archieve', []),
        ];
    }

    public function bootUsing()
    {
        app()->scopeCurrentConference();

        PanelProvider::setupFilamentComponent();

        Block::registerBlocks([
            new SearchBlock,
            new LoginBlock,
            new CalendarBlock,
            new UpcomingConferenceBlock,
            new TopicBlock,
            new TimelineBlock,
        ]);
        Block::boot();
    }
}
