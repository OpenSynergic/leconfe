<?php

namespace App\Panel\Livewire\Submissions\Components\Discussions;

use App\Actions\Submissions\CreateDiscussionTopic;
use App\Infolists\Components\LivewireEntry;
use App\Models\Enums\SubmissionStage;
use App\Models\Submission;
use Awcodes\FilamentBadgeableColumn\Components\Badge;
use Awcodes\FilamentBadgeableColumn\Components\BadgeableColumn;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Fieldset;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

class DiscussionTopic extends \Livewire\Component implements HasForms, HasTable
{
    use InteractsWithTable, InteractsWithForms;

    public Submission $submission;

    public SubmissionStage $stage;

    public function mount(Submission $submission, SubmissionStage $stage)
    {
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Discussion')
            ->query(fn () => $this->submission->discussionTopics()->where('stage', $this->stage))
            ->actions([
                ActionGroup::make([
                    Action::make('open-discussion-detail')
                        ->icon('lineawesome-eye-solid')
                        ->label("Details")
                        ->modalWidth("6xl")
                        ->modalHeading(fn (Model $discussionTopic): string => "Discussion for topic {$discussionTopic->name}")
                        ->modalSubmitAction(false)
                        ->infolist(function (Model $discussionTopic) {
                            return [
                                LivewireEntry::make('discussion-detail')
                                    ->livewire(
                                        DiscussionDetail::class,
                                        ['topic' => $discussionTopic]
                                    )->lazy(),
                                Fieldset::make('form-discussion-detail')
                                    ->label("Add Message")
                                    ->columns(1)
                                    ->schema([
                                        LivewireEntry::make('discussion-detail-form')
                                            ->livewire(
                                                DiscussionDetailForm::class,
                                                ['topic' => $discussionTopic]
                                            )->lazy()
                                    ]),
                            ];
                        }),
                    Action::make('update-status-action')
                        ->authorize('DiscussionTopic:update')
                        ->label(fn ($record): string => $record->open ? "Close" : "Open")
                        ->color(fn ($record): string => $record->open ? 'warning' : 'success')
                        ->icon(fn ($record): string => $record->open ? 'lineawesome-lock-solid' : 'lineawesome-unlock-solid')
                        ->requiresConfirmation()
                        ->successNotificationTitle("Topic updated successfully")
                        ->action(function (Action $action, $record) {
                            $record->update(['open' => !$record->open]);
                            $action->success();
                        }),
                    DeleteAction::make()
                        ->authorize('DiscussionTopic:delete'),
                ])
            ])
            ->headerActions([
                Action::make('create-topic')
                    ->authorize('DiscussionTopic:create')
                    ->icon("lineawesome-plus-solid")
                    ->label("Topic")
                    ->modalWidth("xl")
                    ->form([
                        TextInput::make('name')
                            ->label('Topic Name')
                            ->placeholder('Topic Name')
                            ->required(),
                        CheckboxList::make('user_id')
                            ->label('Participants')
                            ->default([$this->submission->user->getKey()])
                            ->options(function () {
                                return $this->submission->participants()
                                    ->with(['user', 'role'])
                                    ->get()
                                    ->mapWithKeys(function ($participant) {
                                        return [$participant->user->getKey() => $participant->user->fullName];
                                    });
                            })
                            ->descriptions(function () {
                                return $this->submission->participants()
                                    ->with(['user', 'role'])
                                    ->get()
                                    ->mapWithKeys(function ($participant) {
                                        return [$participant->user->getKey() => $participant->role->name];
                                    });
                            })
                    ])
                    ->successNotificationTitle("Topic created successfully")
                    ->failureNotificationTitle("Topic creation failed")
                    ->action(function (Action $action, array $data, Form $form) {
                        $form->validate();
                        try {
                            CreateDiscussionTopic::run(
                                $this->submission,
                                [
                                    'name' => $data['name'],
                                    'stage' => $this->stage
                                ],
                                $data['user_id']
                            );
                            $action->success();
                        } catch (\Throwable $th) {
                            $action->failure();
                        }
                    })
            ])
            ->columns([
                BadgeableColumn::make('name')
                    ->suffixBadges([
                        Badge::make('status')
                            ->label(fn ($record) => $record->open ? 'Open' : 'Closed')
                            ->color(fn ($record) => $record->open ? 'success' : 'danger')
                    ]),
                TextColumn::make('Last Update')
                    ->getStateUsing(fn ($record) => $record->getLastSender()?->fullName)
                    ->description(function ($record): ?string {
                        return "{$record->getLastUpdate()}";
                    })
            ]);
    }

    public function render()
    {
        return view('panel.livewire.submissions.components.discussions.discussion-topic');
    }
}
