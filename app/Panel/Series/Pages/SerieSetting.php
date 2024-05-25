<?php

namespace App\Panel\Series\Pages;

use Filament\Infolists\Infolist;
use Filament\Pages\Page;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Filament\Infolists\Components\Tabs;
use App\Infolists\Components\VerticalTabs as InfolistsVerticalTabs;
use App\Infolists\Components\LivewireEntry;
use App\Panel\Series\Livewire\InformationSetting;
use App\Panel\Series\Livewire\SponsorSetting;

class SerieSetting extends Page
{
    protected static string $view = 'panel.series.pages.serie-setting';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationIcon = 'heroicon-o-window';

    protected static ?string $navigationLabel = 'Serie';

    public function mount(): void
    {
        $this->authorize('update', App::getCurrentSerie());
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()->can('update', App::getCurrentConference());
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfolistsVerticalTabs\Tabs::make()
                    ->schema([
                        InfolistsVerticalTabs\Tab::make('Information')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                LivewireEntry::make('information-setting')
                                    ->livewire(InformationSetting::class)
                            ]),
                        InfolistsVerticalTabs\Tab::make('Sponsors')
                            ->icon("lineawesome-users-solid")
                            ->schema([
                                LivewireEntry::make('sponsors-setting')
                                    ->livewire(SponsorSetting::class),
                            ])
                    ]),
            ]);
    }
}
