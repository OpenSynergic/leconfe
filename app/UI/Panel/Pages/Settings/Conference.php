<?php

namespace App\UI\Panel\Pages\Settings;

use Closure;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Route;

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
                            ->schema([]),
                        Tabs\Tab::make('Topics')
                            ->schema([]),
                        Tabs\Tab::make('Speakers')
                            ->schema([]),
                        Tabs\Tab::make('Venues')
                            ->schema([]),
                    ])
                    ->contained(false),
            ]);
    }
}
