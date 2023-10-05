<?php

namespace App\Panel\Livewire\Submissions\Components;

use App\Models\Enums\UserRole;
use App\Models\ParticipantPosition;
use App\Models\Submission;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\Action;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Livewire\Component;

class ReviewerList extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public Submission $record;

    public function table(Table $table): Table
    {
        return $table
            ->query(function (): Builder {
                $reviewerPosition = ParticipantPosition::where('name', UserRole::Reviewer->value)->first();
                return $this->record->participants()->where('participant_position_id', $reviewerPosition->getKey());
            })
            ->heading("Reviewer List")
            ->headerActions([
                Action::make('add-reviewer')
                    ->label("Reviewer")
                    ->form([])
            ])
            ->columns([]);
    }
    public function render()
    {
        return view('panel.livewire.submissions.components.reviewer-list');
    }
}
