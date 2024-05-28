<?php

namespace App\Panel\Administration\Pages;

use App\Infolists\Components\LivewireEntry;
use App\Infolists\Components\VerticalTabs;
use App\Panel\Administration\Livewire\EmailSetting;
use App\Panel\Administration\Livewire\ErrorReportSetting;
use App\Panel\Administration\Livewire\InformationSetting;
use App\Panel\Administration\Livewire\SetupSetting;
use App\Panel\Administration\Livewire\SidebarSetting;
use App\Panel\Administration\Livewire\SponsorSetting;
use App\Panel\Conference\Livewire\AccessSetting;
use App\Panel\Conference\Livewire\DateAndTimeSetting;
use App\Panel\Conference\Livewire\NavigationMenuSetting;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;

class SiteSettings extends Page implements HasInfolists
{
    use InteractsWithInfolists;

    protected static ?string $navigationIcon = 'heroicon-m-cog';

    protected static string $view = 'panel.administration.pages.site-settings';

    public array $appearanceFormData = [];

    public function mount()
    {
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Tabs::make('site_settings')
                    ->tabs([
                        Tabs\Tab::make('About')
                            ->schema([
                                VerticalTabs\Tabs::make()
                                    ->tabs([
                                        VerticalTabs\Tab::make('Information')
                                            ->icon('heroicon-o-information-circle')
                                            ->schema([
                                                LivewireEntry::make('access_setting')
                                                    ->livewire(InformationSetting::class)
                                                    ->lazy(),
                                            ]),
                                    ]),
                            ]),

                        Tabs\Tab::make('Appearance')
                            ->schema([
                                VerticalTabs\Tabs::make()
                                    ->tabs([
                                        VerticalTabs\Tab::make('Setup')
                                            ->icon('heroicon-o-adjustments-horizontal')
                                            ->schema([
                                                LivewireEntry::make('sidebar_setting')
                                                    ->livewire(SetupSetting::class)
                                                    ->lazy(),
                                            ]),
                                        VerticalTabs\Tab::make('Sidebar')
                                            ->icon('heroicon-o-view-columns')
                                            ->schema([
                                                LivewireEntry::make('sidebar_setting')
                                                    ->livewire(SidebarSetting::class)
                                                    ->lazy(),
                                            ]),
                                        VerticalTabs\Tab::make('Navigation Menu')
                                            ->icon('heroicon-o-list-bullet')
                                            ->schema([
                                                LivewireEntry::make('navigation-menu-setting')
                                                    ->livewire(NavigationMenuSetting::class)
                                                    ->lazy(),
                                            ]),

                                    ]),
                            ]),

                        Tabs\Tab::make('System')
                            ->schema([
                                VerticalTabs\Tabs::make()
                                    ->tabs([
                                        VerticalTabs\Tab::make('Access Options')
                                            ->icon('heroicon-o-information-circle')
                                            ->schema([
                                                LivewireEntry::make('access_setting')
                                                    ->livewire(AccessSetting::class)
                                                    ->lazy(),
                                            ]),
                                        VerticalTabs\Tab::make('Date & Time')
                                            ->icon('heroicon-o-clock')
                                            ->schema([
                                                LivewireEntry::make('date_and_time')
                                                    ->livewire(DateAndTimeSetting::class)
                                                    ->lazy(),
                                            ]),
                                        VerticalTabs\Tab::make('Error Reporting')
                                            ->icon('heroicon-o-exclamation-triangle')
                                            ->schema([
                                                LivewireEntry::make('error_report_setting')
                                                    ->livewire(ErrorReportSetting::class)
                                                    ->lazy(),
                                            ]),
                                    ]),
                            ]),
                    ])
                    ->contained(false),
            ]);
    }
}
