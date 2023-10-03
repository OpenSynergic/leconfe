<?php

namespace App\Panel\Pages\Settings;

use App\Infolists\Components\LivewireEntry;
use App\Infolists\Components\VerticalTabs\Tab;
use App\Infolists\Components\VerticalTabs\Tabs;
use App\Panel\Livewire\Workflows\AbstractList;
use App\Panel\Livewire\Workflows\AbstractSetting;
use App\Panel\Livewire\Workflows\PeerReviewSetting;
use App\Panel\Resources\SubmissionResource;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Tabs as HorizontalTabs;
use Filament\Infolists\Components\Tabs\Tab as HorizontalTab;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;

class Workflow extends Page implements HasInfolists, HasForms
{
    use InteractsWithInfolists;
    use InteractsWithForms;

    protected static ?int $navigationSort = 1;

    protected static string $view = 'panel.pages.workflow';

    protected static ?string $navigationIcon = 'heroicon-o-window';

    protected static ?string $navigationGroup = 'Settings';


    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Tabs::make()
                ->tabs([
                    Tab::make("Call for Abstract")
                        ->icon("iconpark-documentfolder-o")
                        ->schema([
                            Section::make()
                                ->schema([
                                    LivewireEntry::make("abstract-setting")
                                        ->livewire(AbstractSetting::class)
                                ])
                        ]),
                    Tab::make("Peer Review")
                        ->icon("iconpark-search-o")
                        ->schema([
                            Section::make()
                                ->schema([
                                    LivewireEntry::make("peer-review-setting")
                                        ->livewire(PeerReviewSetting::class)
                                ])
                        ]),
                    Tab::make("Editing")
                        ->icon("iconpark-paperclip")
                        ->schema([]),
                ])
                ->maxWidth('full'),
        ]);
    }
}
