<?php

namespace App\Panel\Pages;

use App\Infolists\Components\LivewireEntry;
use App\Infolists\Components\VerticalTabs\Tab;
use App\Infolists\Components\VerticalTabs\Tabs;
use App\Panel\Livewire\Workflows\AbstractList;
use App\Panel\Livewire\Workflows\AbstractSetting;
use App\Panel\Livewire\Workflows\PeerReviewSetting;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
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

    protected ?string $heading = 'Workflow';

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Tabs::make()
                ->tabs([
                    Tab::make("Abstract")
                        ->icon("iconpark-documentfolder-o")
                        ->schema([
                            HorizontalTabs::make()
                                ->tabs([
                                    HorizontalTab::make("Setting")
                                        ->icon("iconpark-setting-o")
                                        // ->hidden(function (): bool {
                                        //     return !auth()->user()->can('WorkflowSetting:update');
                                        // })
                                        ->schema([
                                            LivewireEntry::make("abstract-setting")
                                                ->livewire(AbstractSetting::class)
                                        ]),
                                    HorizontalTab::make("List")
                                        ->icon("heroicon-o-list-bullet")
                                        ->schema([
                                            LivewireEntry::make('abstract-list')
                                                ->livewire(AbstractList::class)
                                        ])
                                ])
                        ]),
                    Tab::make("Peer Review")
                        ->icon("iconpark-search-o")
                        ->schema([
                            HorizontalTabs::make()
                                ->tabs([
                                    HorizontalTab::make("Setting")
                                        ->icon("iconpark-setting-o")
                                        ->schema([
                                            LivewireEntry::make("peer-review-setting")
                                                ->livewire(PeerReviewSetting::class)
                                        ]),
                                    HorizontalTab::make("List")
                                        ->icon("heroicon-o-list-bullet")
                                        ->schema([]),
                                ])
                        ]),
                    Tab::make("Editing")
                        ->icon("iconpark-paperclip"),
                ])
                ->maxWidth('full')
        ]);
    }
}
