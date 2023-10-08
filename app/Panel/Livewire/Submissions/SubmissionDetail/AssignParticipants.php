<?php

namespace App\Panel\LiveWire\Submissions\SubmissionDetail;

use App\Actions\Submissions\SubmissionAssignParticipantAction;
use App\Models\Enums\UserRole;
use App\Models\Participant;
use App\Models\ParticipantPosition;
use App\Models\Submission;
use App\Models\User;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Get;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;
use STS\FilamentImpersonate\Tables\Actions\Impersonate;

class AssignParticipants extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public Submission $submission;

    public array $selectedParticipant = [];

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
                Split::make([
                    SpatieMediaLibraryImageColumn::make('participant.profile')
                        ->grow(false)
                        ->collection('profile')
                        ->conversion('avatar')
                        ->width(50)
                        ->height(50)
                        ->defaultImageUrl(function (Model $record): string {
                            $participant = $record->participant;
                            $name = str($participant->fullName)
                                ->trim()
                                ->explode(' ')
                                ->map(fn (string $segment): string => filled($segment) ? mb_substr($segment, 0, 1) : '')
                                ->join(' ');
                            return 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&color=FFFFFF&background=111827&font-size=0.33';
                        })
                        ->extraCellAttributes([
                            'style' => 'width: 1px',
                        ])
                        ->circular(),
                    TextColumn::make('participant.fullName')
                        ->description(
                            function (Model $record) {
                                return $record->position()->first()->name;
                            }
                        )
                ])
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
                                    ->required()
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
                                                fn (Participant $participant) => [
                                                    $participant->getKey() => static::renderSelectParticipant($participant)
                                                ]
                                            )
                                            ->toArray()
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->columnSpan(2),
                                Checkbox::make('send-notification')
                                    ->label("Send Notification")
                                    ->reactive()
                                    ->columnSpanFull(),
                                Fieldset::make()
                                    ->visible(fn (Get $get): bool => $get('send-notification'))
                                    ->label("Notification")
                                    ->schema([
                                        RichEditor::make("message")
                                            ->columnSpanFull()
                                    ])
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

                        if ($data['send-notification']) {
                            // Send Notification
                        }

                        $action->success();
                    })
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('notify-participant')
                        ->color('primary')
                        ->modalHeading("Notify Participant")
                        ->icon("iconpark-sendemail")
                        ->modalSubmitActionLabel("Notify")
                        ->modalWidth('xl')
                        ->visible(
                            fn (Model $record): bool => $record->participant->email !== auth()->user()->email
                        )
                        ->form([
                            Grid::make(1)
                                ->schema([
                                    TextInput::make('email')
                                        ->disabled()
                                        ->formatStateUsing(function (Model $record) {
                                            return $record->participant->email;
                                        })
                                        ->label("Target"),
                                    RichEditor::make("message")
                                        ->label("Message")
                                        ->columnSpanFull()
                                ])
                        ])
                        ->label("Notify"),
                    Impersonate::make()
                        ->grouped()
                        ->visible(
                            fn (Model $record): bool => $record->participant->email !== auth()->user()->email && auth()->user()->canImpersonate()
                        )
                        ->label("Login as")
                        ->icon("iconpark-login")
                        ->color('primary')
                        ->redirectTo('panel')
                        ->action(function (Model $record, Impersonate $action) {
                            $user = User::where('email', $record->participant->email)->first();
                            if (!$user) {
                                $action->failureNotificationTitle("User not Found");
                                $action->failure();
                            }
                            if (!$action->impersonate($user)) {
                                $action->failureNotificationTitle("User can't be impersonated");
                                $action->failure();
                            }
                        }),
                    Action::make('remove-participant')
                        ->color('danger')
                        ->icon("iconpark-deletethree-o")
                        ->visible(
                            fn (Model $record): bool => $record->participant->email !== auth()->user()->email
                        )
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
