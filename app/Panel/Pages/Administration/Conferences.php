<?php

namespace App\Panel\Pages\Administration;

use App\Infolists\Components\LivewireEntry;
use App\Livewire\Panel\Tables\ConferenceTable;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;

class Conferences extends Page implements HasForms, HasInfolists
{
    use InteractsWithInfolists;
    use InteractsWithForms;

    protected static ?string $navigationGroup = 'Administration';

    protected static ?string $navigationIcon = 'heroicon-o-window';

    protected static string $view = 'panel.pages.administration.conferences';

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                LivewireEntry::make('conference-table', ConferenceTable::class)
                    ->lazy()
            ]);
    }
}
