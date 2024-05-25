<?php

namespace App\Panel\Conference\Livewire\Submissions\Components\Discussions;

use App\Actions\Submissions\CreateDiscussionTopic;
use App\Actions\Submissions\UpdateDiscussionTopic;
use App\Infolists\Components\LivewireEntry;
use App\Models\Enums\SubmissionStage;
use App\Models\Submission;
use App\Notifications\NewDiscussionTopic;
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
use Illuminate\Support\Facades\Auth;

class DiscussionTopic extends \Livewire\Component implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    public Submission $submission;

    public SubmissionStage $stage;

    public function mount(Submission $submission, SubmissionStage $stage)
    {
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('name')
                ->label('Topic Name')
                ->placeholder('Topic Name')
                ->required(),
            CheckboxList::make('user_id')
                ->label('Participants')
                ->default([Auth::id()])
                ->rules('required|array|min:2')
                ->disableOptionWhen(fn ($value): bool => $value == Auth::id()) // Can't remove self from participant
                ->options(function () {
                    $submissionParticipant = $this->submission->participants()
                        ->with(['user'])
                        ->get()
                        ->mapWithKeys(function ($participant) {
                            return [$participant->user->getKey() => $participant->user->fullName];
                        })->toArray();

                    if (! isset($submissionParticipant[Auth::id()])) {
                        $submissionParticipant[Auth::id()] = Auth::user()->fullName;
                    }

                    return $submissionParticipant;
                })
                ->descriptions(function () {
                    $submissionParticipant = $this->submission->participants()
                        ->with(['user', 'role'])
                        ->get()
                        ->mapWithKeys(function ($participant) {
                            return [$participant->user->getKey() => $participant->role->name];
                        })->toArray();

                    if (! isset($submissionParticipant[Auth::id()])) {
                        $submissionParticipant[Auth::id()] = 'Unassigned';
                    }

                    return $submissionParticipant;
                }),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Discussion')
            ->query(fn () => $this->submission->discussionTopics()->where('stage', $this->stage))
            ->recordAction('open-discussion-detail')
            ->actions([
                ActionGroup::make([
                    Action::make('open-discussion-detail')
                        ->icon('lineawesome-eye-solid')
                        ->label('Details')
                        ->modalWidth('6xl')
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
                                    ->label('Add Message')
                                    ->columns(1)
                                    ->schema([
                                        LivewireEntry::make('discussion-detail-form')
                                            ->livewire(
                                                DiscussionDetailForm::class,
                                                ['topic' => $discussionTopic]
                                            )->lazy(),
                                    ]),
                            ];
                        }),
                    Action::make('update-topic')
                        ->label('Edit')
                        ->icon('lineawesome-edit-solid')
                        ->mountUsing(function ($record, Form $form) {
                            $form->fill([
                                'name' => $record->name,
                                'user_id' => $record->participants()->pluck('user_id')->toArray(),
                            ]);
                        })
                        ->authorize('DiscussionTopic:update')
                        ->form($this->getFormSchema())
                        ->successNotificationTitle('Topic updated successfully')
                        ->action(function (Action $action, array $data, Model $record) {
                            UpdateDiscussionTopic::run(
                                $record,
                                ['name' => $data['name']],
                                $data['user_id']
                            );
                            $action->success();
                        }),
                    Action::make('update-status-action')
                        ->authorize('DiscussionTopic:update')
                        ->label(fn ($record): string => $record->open ? 'Close' : 'Open')
                        ->color(fn ($record): string => $record->open ? 'warning' : 'success')
                        ->icon(fn ($record): string => $record->open ? 'lineawesome-lock-solid' : 'lineawesome-unlock-solid')
                        ->requiresConfirmation()
                        ->successNotificationTitle('Topic updated successfully')
                        ->action(function (Action $action, $record) {
                            $record->update(['open' => ! $record->open]);
                            $action->success();
                        }),
                    DeleteAction::make()
                        ->authorize('DiscussionTopic:delete'),
                ]),
            ])
            ->headerActions([
                Action::make('create-topic')
                    ->authorize('DiscussionTopic:create')
                    ->icon('lineawesome-plus-solid')
                    ->outlined()
                    ->label('Topic')
                    ->modalWidth('xl')
                    ->form($this->getFormSchema())
                    ->successNotificationTitle('Topic created successfully')
                    ->failureNotificationTitle('Topic creation failed')
                    ->action(function (Action $action, array $data, Form $form) {
                        $form->validate();

                        $topic = CreateDiscussionTopic::run(
                            $this->submission,
                            [
                                'name' => $data['name'],
                                'stage' => $this->stage,
                            ],
                            $data['user_id']
                        );

                        try {
                            $topic->participants()
                                ->with('user')
                                ->get()
                                ->each(function ($participant) use ($topic) {
                                    $participant->user->notify(
                                        new NewDiscussionTopic($topic)
                                    );
                                });
                        } catch (\Throwable $th) {
                            $action->failureNotificationTitle('Failed to send notification to participants.');
                            $action->failure();
                        } finally {
                            $action->success();
                        }
                    }),
            ])
            ->columns([
                BadgeableColumn::make('name')
                    ->suffixBadges([
                        Badge::make('status')
                            ->label(fn ($record) => $record->open ? 'Open' : 'Closed')
                            ->color(fn ($record) => $record->open ? 'success' : 'danger'),
                    ]),
                TextColumn::make('Last Update')
                    ->getStateUsing(fn ($record) => $record->getLastSender()?->fullName ?? '-')
                    ->description(fn ($record): ?string => $record->getLastUpdate()),
            ]);
    }

    public function render()
    {
        return view('panel.conference.livewire.submissions.components.discussions.discussion-topic');
    }
}
