<?php

namespace App\Panel\Conference\Pages\Settings;

use App\Infolists\Components\LivewireEntry;
use App\Infolists\Components\VerticalTabs as InfolistsVerticalTabs;
use App\Panel\Conference\Livewire\Forms\Conferences\ContactSetting;
use App\Panel\Conference\Livewire\Forms\Conferences\InformationSetting;
use App\Panel\Conference\Livewire\Forms\Conferences\PrivacySetting;
use App\Panel\Conference\Livewire\Forms\Conferences\SearchEngineSetting;
use App\Panel\Conference\Livewire\Forms\Conferences\SetupSetting;
use App\Panel\Conference\Livewire\Forms\Conferences\SidebarSetting;
use App\Panel\Conference\Livewire\Forms\Conferences\SponsorSetting;
use App\Panel\Conference\Livewire\NavigationMenuSetting;
use App\Panel\Conference\Pages\BasePage;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class ConferenceSetting extends BasePage implements HasForms, HasInfolists
{
    use InteractsWithForms, InteractsWithInfolists;

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationIcon = 'heroicon-o-window';

    protected static string $view = 'panel.conference.pages.settings.conference';

    protected ?string $heading = 'Conference Settings';

    protected static ?string $navigationLabel = 'Conference';

    public function mount(): void
    {
        $this->authorize('update', App::getCurrentConference());
    }

    public static function getNavigationUrl(): string
    {
        return static::getUrl(['conference' => app()->getCurrentConference()->path]);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()->can('update', App::getCurrentConference());
    }

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
                                        InfolistsVerticalTabs\Tab::make('Sponsors')
                                            ->icon("heroicon-o-currency-dollar")
                                            ->schema([
                                                LivewireEntry::make('sponsors-setting')
                                                    ->livewire(SponsorSetting::class, [
                                                        'conference' => App::getCurrentConference(),
                                                    ]),
                                            ])
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
                                        InfolistsVerticalTabs\Tab::make('Navigation Menu')
                                            ->icon('heroicon-o-list-bullet')
                                            ->schema([
                                                LivewireEntry::make('navigation-menu-setting')
                                                    ->livewire(NavigationMenuSetting::class),
                                            ]),
                                    ]),
                            ]),
                        Tabs\Tab::make('Distribution')
                            ->schema([
                                InfolistsVerticalTabs\Tabs::make()
                                    ->schema([
                                        InfolistsVerticalTabs\Tab::make('Search Indexing')
                                            ->icon('heroicon-o-magnifying-glass')
                                            ->schema([
                                                LivewireEntry::make('sidebar-setting')
                                                    ->livewire(SearchEngineSetting::class, [
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
