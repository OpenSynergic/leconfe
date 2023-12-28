<?php

namespace App\Panel\Livewire\Submissions\Components;

use App\Models\Submission;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ActivityLogList extends \Livewire\Component implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    public Submission $submission;

    public function mount(Submission $submission)
    {
    }

    public function table(Table $table)
    {
        return $table
            ->query(
                fn (): Builder => $this->submission->activities()->with('causer')->orderBy('created_at', 'asc')->getQuery()
            )
            ->paginationPageOptions([20, 50, 100])
            ->defaultPaginationPageOption(20)
            ->columns([
                TextColumn::make('created_at')
                    ->label('Date')
                    ->formatStateUsing(function ($state) {
                        return $state->format(setting('format.date')).' '.$state->format(setting('format.time'));
                    })
                    ->description(function ($record) {
                        return $record->created_at->diffForHumans();
                    }),
                TextColumn::make('causer.fullName')
                    ->getStateUsing(function (Model $record) {
                        if (! $record->causer_type) {
                            return 'System';
                        }

                        return $record->causer->fullName;
                    })
                    ->label('Causer Name'),
                TextColumn::make('description'),
            ]);
    }

    public function render()
    {
        return view('panel.livewire.submissions.components.activity-log-list');
    }
}
