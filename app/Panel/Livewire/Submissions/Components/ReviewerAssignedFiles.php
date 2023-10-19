<?php

namespace App\Panel\Livewire\Submissions\Components;

use App\Models\Review;
use App\Models\ReviewerAssignedFile;
use App\Models\SubmissionFileType;
use App\Models\User;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\Database\Eloquent\Builder;

class ReviewerAssignedFiles extends \Livewire\Component implements HasTable, HasForms
{
    use InteractsWithTable, InteractsWithForms;

    public Review $record;

    public User $user;

    public function mount(Review $record, User $user): void
    {
        $this->user = $user ?? auth()->user();
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading("Assigned Files")
            ->query(
                fn (): Builder => $this->record->assignedFiles()->getQuery()
            )
            ->columns([
                TextColumn::make('submissionFile.media.file_name')
                    ->color('primary')
                    ->action(function (ReviewerAssignedFile $record) {
                        return $record->submissionFile->media;
                    })
                    ->description(function (ReviewerAssignedFile $record) {
                        return $record->submissionFile->type->name;
                    })
            ]);
    }

    public function render()
    {
        return view('panel.livewire.submissions.components.reviewer-assigned-files');
    }
}
