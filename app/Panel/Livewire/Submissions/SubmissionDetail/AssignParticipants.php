<?php

namespace App\Panel\LiveWire\Submissions\SubmissionDetail;

use App\Models\Conference;
use App\Models\Enums\UserRole;
use App\Models\Participant;
use App\Models\Submission;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

class AssignParticipants extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public Submission $submission;

    public function mount(Submission $submission)
    {
        $this->submission = $submission;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn (): Builder => $this->submission->participants()->getQuery()
            )
            ->columns([
                TextColumn::make('participant.fullName')
                    ->description(
                        fn (Model $record): string => $record->position->name
                    )
            ])
            ->heading('Participants')
            ->headerActions([
                Action::make("Assign Participant")
                    ->size('xs')
                    ->modalWidth("lg")
                    ->form([
                        Select::make('participant_id')
                            ->label("Name")
                            ->options(
                                fn (): array => Participant::with('positions')
                                    ->whereHas('positions', function ($query) {
                                        return $query->whereIn('name', [
                                            UserRole::Reviewer->value,
                                            UserRole::TrackDirector->value,
                                        ]);
                                    })
                                    ->get()
                                    ->mapWithKeys(
                                        fn ($participant) => [$participant->id => $participant->fullName]
                                    )
                                    ->toArray()
                            )
                            ->searchable()
                            ->preload()
                    ])
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('notify-participant')
                        ->color('primary')
                        ->label("Notify"),
                    Action::make('login-as-participant')
                        ->color('primary')
                        ->label("Login As"),
                    Action::make('edit-participant')
                        ->label('Edit'),
                    Action::make('remove-participant')
                        ->color('danger')
                        ->label("Remove"),
                ])
            ])
            ->paginated(false);
    }

    public function render()
    {
        return view('panel.livewire.submissions.submission-detail.assign-participants');
    }
}
