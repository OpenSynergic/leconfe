<?php

namespace App\Panel\LiveWire\Submissions\SubmissionDetail;

use App\Actions\Submissions\SubmissionAssignParticipantAction;
use App\Models\Enums\UserRole;
use App\Models\Participant;
use App\Models\ParticipantPosition;
use App\Models\Submission;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Get;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\CreateAction;
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
                        function (Model $record) {
                            return $record->position()->first()->name;
                        }
                    )
            ])
            ->heading('Participants')
            ->headerActions([
                CreateAction::make()
                    ->modalHeading("Assign Participant")
                    ->icon("lineawesome-user-plus-solid")
                    ->label("Assign")
                    ->size('xs')
                    ->extraModalFooterActions(function (Action $action) {
                        return [$action->makeModalSubmitAction('assignAnother', ['another' => true])
                            ->label("Assign this & Assign another")];
                    })
                    ->modalSubmitActionLabel("Assign")
                    ->modalWidth("2xl")
                    ->form([
                        Grid::make(3)
                            ->schema([
                                Select::make('position')
                                    ->label("Position")
                                    ->options(function () {
                                        return ParticipantPosition::whereIn('name', [
                                            UserRole::Editor->value,
                                            UserRole::Author->value
                                        ])
                                            ->get()
                                            ->pluck('name', 'id');
                                    })
                                    ->selectablePlaceholder("Select Position")
                                    ->columnSpan(1),
                                Select::make('participant_id')
                                    ->label("Participant")
                                    ->allowHtml()
                                    ->reactive()
                                    ->preload()
                                    ->reactive()
                                    ->options(
                                        fn (Get $get): array => Participant::with('positions')
                                            ->whereHas(
                                                'positions',
                                                function ($query) use ($get) {
                                                    return $query->whereId($get('position'));
                                                }
                                            )
                                            ->whereNotIn('id', $this->submission->participants->pluck('participant_id'))
                                            ->get()
                                            ->mapWithKeys(
                                                fn (Participant $participant) => [$participant->getKey() => static::renderSelectParticipant($participant)]
                                            )
                                            ->toArray()
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->columnSpan(2)
                            ])
                    ])
                    ->successNotificationTitle("Participant Assigned")
                    ->action(function (Action $action, array $data) {
                        $participant = Participant::find($data['participant_id']);
                        $participantPosition = ParticipantPosition::find($data['position']);
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
                    Action::make('remove-participant')
                        ->color('danger')
                        ->label("Remove")
                        ->successNotificationTitle("Participant Removed")
                        ->action(function (Action $action, Model $record) {
                            $record->delete();
                            $action->success();
                        })
                        ->requiresConfirmation(),
                ])
            ])
            ->paginated(false);
    }

    public function render()
    {
        return view('panel.livewire.submissions.submission-detail.assign-participants');
    }
}
