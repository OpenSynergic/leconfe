<?php

namespace App\Panel\Pages\Settings;

use App\Infolists\Components\LivewireEntry;
use App\Livewire\SpeakerTable;
use App\Livewire\TopicTable;
use App\Livewire\VenueTable;
use App\Models\Speaker;
use App\Models\Topic;
use App\Models\Venue;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;

class Conference extends Page implements HasInfolists, HasForms
{
    use InteractsWithInfolists, InteractsWithForms;

    protected static ?int $navigationSort = 1;

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
                            ->badge(fn () => Topic::count())
                            ->schema([
                                LivewireEntry::make('topics', TopicTable::class)
                                    ->lazy()
                            ]),
                        Tabs\Tab::make('Speakers')
                            ->icon('heroicon-m-users')
                            ->badge(fn () => Speaker::count())
                            ->schema([
                                LivewireEntry::make('topics', SpeakerTable::class)
                                    ->lazy()
                            ]),
                        Tabs\Tab::make('Venues')
                            ->icon('heroicon-m-home-modern')
                            ->badge(fn () => Venue::count())
                            ->schema([
                                LivewireEntry::make('topics', VenueTable::class)
                                    ->lazy()
                            ]),
                    ])
                    ->contained(false),
            ]);
    }
}
