<?php

namespace App\Panel\Conference\Pages\Settings;

use App\Infolists\Components\LivewireEntry;
use App\Panel\Conference\Livewire\Forms\Conferences\DOISetup;
use App\Panel\Conference\Livewire\Forms\Conferences\SearchEngineSetting;
use App\Infolists\Components\VerticalTabs as InfolistsVerticalTabs;
use App\Panel\Conference\Livewire\Forms\Conferences\DOIRegistration;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;
use Illuminate\Support\Facades\App;


class DistributionSetting extends Page implements HasForms, HasInfolists
{
    use InteractsWithForms, InteractsWithInfolists;

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationIcon = 'heroicon-o-window';

    protected static string $view = 'panel.conference.pages.settings.distribution';

    protected ?string $heading = 'Distribution Settings';

    protected static ?string $navigationLabel = 'Distribution';

    public function mount(): void
    {
        $this->authorize('update', App::getCurrentConference());
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Tabs::make('distribution_settings')
                    ->tabs([
                        Tabs\Tab::make('DOI')
                            ->icon('academicon-doi')
                            ->schema([
                                InfolistsVerticalTabs\Tabs::make()
                                    ->schema([
                                        InfolistsVerticalTabs\Tab::make('Setup')
                                            ->schema([
                                                LivewireEntry::make('doi-setting')
                                                    ->livewire(DOISetup::class),
                                            ]),
                                    ]),
                            ]),
                        Tabs\Tab::make('Search Indexing')
                            ->icon('heroicon-o-magnifying-glass')
                            ->schema([
                                LivewireEntry::make('sidebar-setting')
                                    ->livewire(SearchEngineSetting::class, [
                                        'conference' => App::getCurrentConference(),
                                    ]),
                            ]),
                    ])
                    ->contained(false),
            ]);
    }
}
