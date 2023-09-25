<?php

namespace App\Panel\Resources\WorkflowResource\Pages;

use App\Infolists\Components\LivewireEntry;
use App\Infolists\Components\VerticalTabs\Tab;
use App\Infolists\Components\VerticalTabs\Tabs;
use App\Panel\Resources\WorkflowResource;
use App\Panel\Livewire\Workflows\AbstractSetting;
use App\Panel\Livewire\Workflows\PeerReviewReviewers;
use App\Panel\Livewire\Workflows\PeerReviewSetting;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\Tabs as HorizontalTabs;
use Filament\Infolists\Components\Tabs\Tab as HorizontalTab;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\Page;

class Index extends Page implements HasInfolists, HasForms
{
    use InteractsWithInfolists;
    use InteractsWithForms;

    protected static string $resource = WorkflowResource::class;

    protected static string $view = 'panel.resources.workflow-resource.pages.index';

    public function abstract()
    {
    }

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
                                        ->schema([
                                            LivewireEntry::make("abstract-setting")
                                                ->livewire(AbstractSetting::class)
                                        ]),
                                    HorizontalTab::make("List")
                                        ->icon("heroicon-o-list-bullet")
                                        ->schema([])
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
                                    HorizontalTab::make('Reviewers')
                                        ->icon('heroicon-o-user-group')
                                        ->schema([
                                            LivewireEntry::make('peer-review-reviewers')
                                                ->livewire(PeerReviewReviewers::class)
                                        ]),
                                    HorizontalTab::make("List")
                                        ->icon("heroicon-o-list-bullet")
                                        ->schema([]),
                                ])
                        ]),
                    Tab::make("Editorial Decision")
                        ->icon("iconpark-paperclip"),
                ])
                ->maxWidth('full')
        ]);
    }
}
