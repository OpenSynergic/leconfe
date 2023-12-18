<?php

namespace App\Panel\Pages;

use App\Models\Enums\SubmissionStatus;
use App\Models\Submission;
use Filament\Pages\Page;
use Filament\Infolists\Infolist;
use App\Panel\Widgets\StatsOverview;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;

class Statistic extends Page implements HasForms, HasInfolists
{
    use InteractsWithInfolists, InteractsWithForms;


    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';

    protected static ?string $navigationGroup = 'Statistic';


    protected static string $view = 'panel.pages.statistic';

    protected function getHeaderWidgets(): array
    {
        return [
            StatsOverview::class
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {


        return $infolist
            ->state($this->getState())
            ->schema([
                Section::make([
                    TextEntry::make('submission.incomplete')
                        ->label('Name'),
                    TextEntry::make('total.incomplete')
                        ->label('Total'),
                    TextEntry::make('submission.queued')
                        ->label('')
                        ->extraAttributes($this->attribute()),
                    TextEntry::make('total.queued')
                        ->label('')
                        ->extraAttributes($this->attribute()),
                    TextEntry::make('submission.onReview')
                        ->label('')
                        ->extraAttributes($this->attribute()),
                    TextEntry::make('total.onReview')
                        ->label('')
                        ->extraAttributes($this->attribute()),
                    TextEntry::make('submission.editing')
                        ->label('')
                        ->extraAttributes($this->attribute()),
                    TextEntry::make('total.editing')
                        ->label('')
                        ->extraAttributes($this->attribute()),
                    TextEntry::make('submission.published')
                        ->label('')
                        ->extraAttributes($this->attribute()),
                    TextEntry::make('total.published')
                        ->label('')
                        ->extraAttributes($this->attribute()),
                    TextEntry::make('submission.declined')
                        ->label('')
                        ->extraAttributes($this->attribute()),
                    TextEntry::make('total.declined')
                        ->label('')
                        ->extraAttributes($this->attribute()),

                ])
                    ->columns(2)


            ]);
    }

    private function attribute(): array
    {
        return ['class' => '-mt-4'];
    }

    private function getState(): array
    {
        return [
            'submission' => [
                'incomplete' => 'Submission Incomplete',
                'queued' => 'Submission Queued',
                'onReview' => 'Submission on Review',
                'editing' => 'Submission Editing',
                'published' => 'Submission Published',
                'declined' => 'Submission Declined',
                'scheduled' => 'Submission Scheduled',
                'withdrawn' => 'Submission Withdrawn'
            ],
            'total' => [
                'incomplete' => Submission::where('status', SubmissionStatus::Incomplete)->count(),
                'queued' => Submission::where('status', SubmissionStatus::Queued)->count(),
                'onReview' => Submission::where('status', SubmissionStatus::OnReview)->count(),
                'editing' => Submission::where('status', SubmissionStatus::Editing)->count(),
                'published' => Submission::where('status', SubmissionStatus::Published)->count(),
                'declined' => Submission::where('status', SubmissionStatus::Declined)->count(),
                'scheduled' => Submission::where('status', SubmissionStatus::Scheduled)->count(),
                'published' => Submission::where('status', SubmissionStatus::Published)->count(),
            ]

        ];
    }
}
