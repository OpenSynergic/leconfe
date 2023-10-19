<?php

namespace App\Panel\Livewire\Submissions\Components;

use App\Models\Submission;
use App\Models\SubmissionContributor;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\Database\Eloquent\Builder;

class ContributorList extends \Livewire\Component implements HasTable, HasForms
{
    use InteractsWithForms, InteractsWithTable;

    public Submission $submission;

    public bool $viewOnly = false;

    public function table(Table $table): Table
    {
        return $table
            ->heading("Contributors")
            ->query(
                fn (): Builder => $this->submission->contributors()->getQuery()
            )
            ->columns([
                Split::make([
                    SpatieMediaLibraryImageColumn::make('participant.profile')
                        ->grow(false)
                        ->collection('profile')
                        ->conversion('avatar')
                        ->width(50)
                        ->height(50)
                        ->defaultImageUrl(
                            fn (SubmissionContributor $record): string => $record->participant->getFilamentAvatarUrl()
                        )
                        ->extraCellAttributes([
                            'style' => 'width: 1px',
                        ])
                        ->circular()
                        ->toggleable(!$this->viewOnly),
                    Stack::make([
                        TextColumn::make('participant.fullName')
                            ->formatStateUsing(function (SubmissionContributor $record) {
                                if ($record->participant->email == auth()->user()->email) {
                                    return $record->participant->fullName . " (You)";
                                }
                                return $record->participant->fullName;
                            }),
                        TextColumn::make('affiliation')
                            ->size("xs")
                            ->getStateUsing(
                                fn (SubmissionContributor $record) => $record->participant->getMeta('affiliation')
                            )
                            ->icon("heroicon-o-building-library")
                            ->extraAttributes([
                                'class' => 'text-xs',
                            ])
                            ->color('gray'),
                        TextColumn::make('participant.email')
                            ->size("xs")
                            ->extraAttributes([
                                'class' => 'text-xs',
                            ])
                            ->color('gray')
                            ->icon('heroicon-o-envelope')
                            ->alignStart(),
                    ])->space(1),
                    TextColumn::make("position")
                        ->getStateUsing(
                            fn (SubmissionContributor $record) => $record->position->name
                        )
                        ->badge()
                        ->alignEnd()
                ])
            ]);
    }

    public function render()
    {
        return view('panel.livewire.submissions.components.contributor-list');
    }
}
