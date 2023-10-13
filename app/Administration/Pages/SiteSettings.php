<?php

namespace App\Administration\Pages;

use App\Administration\Livewire\AccessSetting;
use App\Administration\Livewire\DateAndTimeSetting;
use App\Administration\Livewire\EmailSetting;
use App\Administration\Livewire\InformationSetting;
use App\Infolists\Components\LivewireEntry;
use App\Infolists\Components\VerticalTabs;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;

class SiteSettings extends Page implements HasInfolists
{
    use InteractsWithInfolists;

    protected static ?string $navigationIcon = 'heroicon-m-cog';

    protected static string $view = 'administration.pages.site-settings';

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
                                    ]),
                            ]),
                        Tabs\Tab::make('E-Mail')
                            ->schema([
                                LivewireEntry::make('mail_setting')
                                    ->livewire(EmailSetting::class)
                                    ->lazy(),
                            ]),
                    ])
                    ->contained(false),
            ]);
    }
}
