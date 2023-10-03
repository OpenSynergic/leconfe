<?php

namespace App\Panel\LiveWire\Submissions\SubmissionDetail;

use App\Actions\Submissions\SubmissionAssignParticipantAction;
use App\Models\Conference;
use App\Models\Enums\UserRole;
use App\Models\Participant;
use App\Models\Submission;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Get;
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

    public array $selectedParticipant = [];

    public function mount(Submission $submission)
    {
        $this->submission = $submission;
    }

    public static function renderSelectParticipant(Participant $participant): string
    {
        return view('forms.select-participant', ['participant' => $participant])->render();
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
                Action::make("Assign")
                    ->size('xs')
                    ->modalWidth("xl")
                    ->form([
                        Grid::make(3)
                            ->schema([
                                Select::make('positions')
                                    ->label("Position")
                                    ->options([
                                        UserRole::Author->value => UserRole::Author->value,
                                        UserRole::TrackDirector->value => UserRole::TrackDirector->value,
                                    ])
                                    ->columnSpan(1)
                                    ->default([UserRole::TrackDirector->value]),
                                Select::make('participant_id')
                                    ->label("Name")
                                    ->allowHtml()
                                    ->reactive()
                                    ->multiple()
                                    ->preload()
                                    ->options(
                                        fn (Get $get): array => Participant::with('positions')
                                            ->whereHas('positions', function ($query) use ($get) {
                                                return $query->where('name', $get('positions'))->orWhere('id', 'IN', $this->selectedParticipant);
                                            })
                                            ->get()
                                            ->mapWithKeys(
                                                fn (Participant $participant) => [$participant->getKey() => static::renderSelectParticipant($participant)]
                                            )
                                            ->toArray()
                                    )
                                    ->afterStateUpdated(fn ($state) => $this->selectedParticipant[] = $state['id'])
                                    ->searchable()
                                    ->preload()
                                    ->columnSpan(2)
                            ])
                    ])
                    ->successNotificationTitle("Participant Assigned")
                    ->action(function (Action $action, array $data) {
                        $participant = Participant::find($data['participant_id']);
                        $participantPosition = $participant->positions;
                        SubmissionAssignParticipantAction::run(
                            $this->submission,
                            $participant,
                            $participantPosition
                        );
                        $action->success();
                    })
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
