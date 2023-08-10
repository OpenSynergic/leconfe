<?php

namespace App\Panel\Pages\Settings;

use Filament\Pages\Page;
use Filament\Infolists\Infolist;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\Tabs;
use App\Livewire\Panel\Tables\TopicTable;
use App\Infolists\Components\LivewireEntry;
use App\Livewire\Panel\Tables\SpeakerTable;
use App\Livewire\Panel\Tables\VenueTable;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Infolists\Concerns\InteractsWithInfolists;

class Conference extends Page implements HasInfolists, HasForms
{
    use InteractsWithInfolists, InteractsWithForms;

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'panel.pages.settings.conference';

    protected ?string $heading = 'Conference Settings';

    public function mount()
    {
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Tabs::make('Label')
                    ->tabs([
                        Tabs\Tab::make('General')
                            ->icon('heroicon-m-window')
                            ->schema([]),
                        Tabs\Tab::make('Topics')
                            ->icon('heroicon-m-chat-bubble-left')
                            ->schema([
                                LivewireEntry::make('topics', TopicTable::class)
                                    ->lazy()
                            ]),
                        Tabs\Tab::make('Speaker')
                            ->icon('heroicon-m-users')
                            ->schema([
                                LiveWireEntry::make('speakers', SpeakerTable::class)
                                    ->lazy()
                            ]),
                        Tabs\Tab::make('Venues')
                            ->icon('heroicon-m-home-modern')
                            ->schema([
                                LivewireEntry::make('venues', VenueTable::class)
                                    ->lazy()
                            ]),
                    ])
                    ->contained(false),
            ]);
    }
}
