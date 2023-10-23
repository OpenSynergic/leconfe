<?php

namespace App\Panel\LiveWire\Submissions\SubmissionDetail;

use App\Actions\Submissions\SubmissionAssignParticipantAction;
use App\Models\Enums\UserRole;
use App\Models\Participant;
use App\Models\ParticipantPosition;
use App\Models\Role;
use App\Models\Submission;
use App\Models\SubmissionParticipant;
use App\Models\User;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
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
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;
use STS\FilamentImpersonate\Tables\Actions\Impersonate;

class AssignParticipants extends Component implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    public Submission $submission;

    public array $selectedParticipant = [];

    public static function renderSelectParticipant(User $participant): string
    {
        return view('forms.select-participant', ['participant' => $participant])->render();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn (): Builder => $this->submission->participants()->with(['role'])->getQuery()
            )
            ->columns([
                Split::make([
                    SpatieMediaLibraryImageColumn::make('user.profile')
                        ->grow(false)
                        ->collection('profile')
                        ->conversion('avatar')
                        ->width(50)
                        ->height(50)
                        ->defaultImageUrl(
                            fn (SubmissionParticipant $record): string => $record->user->getFilamentAvatarUrl()
                        )
                        ->extraCellAttributes([
                            'style' => 'width: 1px',
                        ])
                        ->circular(),
                    TextColumn::make('user.fullName')
                        ->description(
                            function (Model $record) {
                                return $record->role->name;
                            }
                        )
                ])
            ])
            ->heading('Participants')
            ->headerActions([
                CreateAction::make()
                    ->modalHeading("Assign Participant")
                    ->hidden($this->submission->isDeclined())
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
                                Select::make('role_id')
                                    ->label("Role")
                                    ->options(function () {
                                        return Role::whereIn('name', [
                                            UserRole::Editor->value,
                                            UserRole::Author->value
                                        ])
                                            ->get()
                                            ->pluck('name', 'id');
                                    })
                                    ->selectablePlaceholder("Select Position")
                                    ->columnSpan(1),
                                Select::make('user_id')
                                    ->label("Name")
                                    ->required()
                                    ->allowHtml()
                                    ->reactive()
                                    ->preload()
                                    ->reactive()
                                    ->options(
                                        fn (Get $get): array => User::with('roles')
                                            ->whereHas(
                                                'roles',
                                                fn (Builder $query) => $query->whereId($get('role_id'))
                                            )
                                            ->whereNotIn('id', $this->submission->participants->pluck('user_id'))
                                            ->get()
                                            ->mapWithKeys(
                                                fn (User $user) => [
                                                    $user->getKey() => static::renderSelectParticipant($user)
                                                ]
                                            )
                                            ->toArray()
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->columnSpan(2),
                                Checkbox::make('no-notification')
                                    ->label("Don't Send Notification")
                                    ->reactive()
                                    ->columnSpanFull(),
                                Fieldset::make()
                                    ->hidden(fn (Get $get): bool => $get('no-notification'))
                                    ->label("Notification")
                                    ->schema([
                                        TinyEditor::make('message')
                                            ->minHeight(300)
                                            ->columnSpanFull()
                                    ])
                            ])
                    ])
                    ->successNotificationTitle("Participant Assigned")
                    ->action(function (Action $action, array $data) {
                        $this->submission->participants()->create([
                            'user_id' => $data['user_id'],
                            'role_id' => $data['role_id'],
                        ]);

                        if (!$data['no-notification']) {
                            /**
                             * TODO: Send notification
                             */
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
                            fn (Model $record): bool => $record->user->email !== auth()->user()->email
                        )
                        ->form([
                            Grid::make(1)
                                ->schema([
                                    TextInput::make('email')
                                        ->disabled()
                                        ->formatStateUsing(
                                            fn (SubmissionParticipant $record) => $record->participant->email
                                        )
                                        ->label("Target"),
                                    TinyEditor::make('message')
                                        ->minHeight(300)
                                        ->label("Message")
                                        ->columnSpanFull()
                                ])
                        ])
                        ->label("Notify"),
                    Impersonate::make()
                        ->grouped()
                        ->visible(
                            fn (Model $record): bool => $record->user->email !== auth()->user()->email && auth()->user()->canImpersonate()
                        )
                        ->label("Login as")
                        ->icon("iconpark-login")
                        ->color('primary')
                        ->redirectTo('panel')
                        ->action(function (SubmissionParticipant $record, Impersonate $action) {
                            if (!$action->impersonate($record->user)) {
                                $action->failureNotificationTitle("User can't be impersonated");
                                $action->failure();
                            }
                        }),
                    Action::make('remove-participant')
                        ->color('danger')
                        ->icon("iconpark-deletethree-o")
                        ->visible(
                            fn (SubmissionParticipant $record): bool => $record->user->email !== $this->submission->user->email
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