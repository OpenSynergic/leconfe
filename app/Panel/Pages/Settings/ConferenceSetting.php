<?php

namespace App\Panel\Pages\Settings;

use App\Infolists\Components\LivewireEntry;
use App\Infolists\Components\VerticalTabs as InfolistsVerticalTabs;
use App\Panel\Livewire\Forms\Conferences\ContactSetting;
use App\Panel\Livewire\Forms\Conferences\InformationSetting;
use App\Panel\Livewire\Forms\Conferences\PrivacySetting;
use App\Panel\Livewire\Forms\Conferences\SetupSetting;
use App\Panel\Livewire\Forms\Conferences\SidebarSetting;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;
use Illuminate\Support\Facades\App;

class ConferenceSetting extends Page implements HasForms, HasInfolists
{
    use InteractsWithForms, InteractsWithInfolists;

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationIcon = 'heroicon-o-window';

    protected static string $view = 'panel.pages.settings.conference';

    protected ?string $heading = 'Conference Settings';

    protected static ?string $navigationLabel = 'Conference';

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Tabs::make('conference_settings')
                    ->tabs([
                        Tabs\Tab::make('About')
                            ->schema([
                                InfolistsVerticalTabs\Tabs::make()
                                    ->schema([
                                        InfolistsVerticalTabs\Tab::make('Information')
                                            ->icon('heroicon-o-information-circle')
                                            ->schema([
                                                LivewireEntry::make('information-setting')
                                                    ->livewire(InformationSetting::class, [
                                                        'conference' => App::getCurrentConference(),
                                                    ]),
                                            ]),
                                        InfolistsVerticalTabs\Tab::make('Privacy')
                                            ->icon('heroicon-o-shield-check')
                                            ->schema([
                                                LivewireEntry::make('information-setting')
                                                    ->livewire(PrivacySetting::class, [
                                                        'conference' => App::getCurrentConference(),
                                                    ]),
                                            ]),
                                        InfolistsVerticalTabs\Tab::make('Contact')
                                            ->icon('heroicon-o-phone')
                                            ->schema([
                                                LivewireEntry::make('information-setting')
                                                    ->livewire(ContactSetting::class, [
                                                        'conference' => App::getCurrentConference(),
                                                    ]),
                                            ]),
                                    ]),
                            ]),
                        Tabs\Tab::make('Appearance')
                            ->schema([
                                InfolistsVerticalTabs\Tabs::make()
                                    ->schema([
                                        InfolistsVerticalTabs\Tab::make('Setup')
                                            ->icon('heroicon-o-adjustments-horizontal')
                                            ->schema([
                                                LivewireEntry::make('setup-setting')
                                                    ->livewire(SetupSetting::class, [
                                                        'conference' => App::getCurrentConference(),
                                                    ]),
                                            ]),
                                        InfolistsVerticalTabs\Tab::make('Sidebar')
                                            ->icon('heroicon-o-view-columns')
                                            ->schema([
                                                LivewireEntry::make('sidebar-setting')
                                                    ->livewire(SidebarSetting::class, [
                                                        'conference' => App::getCurrentConference(),
                                                    ]),
                                            ]),
                                    ]),
                            ]),
                    ])
                    ->contained(false),
            ]);
    }
}
