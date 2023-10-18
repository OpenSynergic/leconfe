<?php

namespace App\Panel\Livewire\Submissions\Components;

use App\Constants\ReviewerStatus;
use App\Constants\SubmissionStatusRecommendation;
use App\Infolists\Components\LivewireEntry;
use App\Models\Enums\UserRole;
use App\Models\Media;
use App\Models\Participant;
use App\Models\ParticipantPosition;
use App\Models\Review;
use App\Models\Submission;
use App\Models\SubmissionFileType;
use App\Models\User;
use App\Panel\Livewire\Tables\Submissions\SubmissionFilesTable;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;
use STS\FilamentImpersonate\Tables\Actions\Impersonate;

class ReviewerList extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public Submission $record;

    public ParticipantPosition $reviewerPosition;

    public function mount(Submission $record)
    {
        $this->reviewerPosition = ParticipantPosition::where('name', UserRole::Reviewer->value)->first();
    }

    private static function formReviewerSchema(ReviewerList $component, bool $editMode = false): array
    {
        return [
            Select::make('participant_id')
                ->label("Reviewer")
                ->placeholder("Select a reviewer")
                ->allowHtml()
                ->preload()
                ->required()
                ->searchable()
                ->options(function () use ($component, $editMode): array {
                    return Participant::with('positions')
                        ->whereHas('positions', function ($query) use ($component) {
                            $query->where('name', $component->reviewerPosition->name);
                        })
                        ->when(!$editMode, function ($query) use ($component) {
                            $query->whereNotIn(
                                'id',
                                Review::where('submission_id', $component->record->getKey())
                                    ->get()
                                    ->pluck('participant_id')
                                    ->toArray()
                            );
                        })
                        ->get()
                        ->mapWithKeys(function (Participant $participant) {
                            return [$participant->getKey() => static::renderSelectParticipant($participant)];
                        })
                        ->toArray();
                }),
            CheckboxList::make('papers')
                ->label("Files to be reviewed")
                ->hidden(
                    !$component->record->papers()->exists()
                )
                ->options(function () use ($component) {
                    return $component->record
                        ->papers()
                        ->get()
                        ->mapWithKeys(function (Media $paper) {
                            return [
                                $paper->getKey() => Action::make($paper->file_name)
                                    ->label($paper->file_name)
                                    ->url(function () use ($paper) {
                                        return route('private.files', ['uuid' => $paper->uuid]);
                                    })
                                    ->link()
                            ];
                        });
                })
                ->descriptions(function () use ($component) {
                    return $component->record->papers()->get()->mapWithKeys(function ($paper) {
                        return [$paper->getKey() => SubmissionFileType::namebyId($paper->getCustomProperty('type'))];
                    });
                }),
        ];
    }

    public static function renderSelectParticipant(Participant $participant): string
    {
        return view('forms.select-participant', ['participant' => $participant])->render();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn (): Builder => $this->record->reviews()->getQuery()
            )
            ->columns([
                Split::make([
                    SpatieMediaLibraryImageColumn::make('participant.profile')
                        ->grow(false)
                        ->collection('profile')
                        ->conversion('avatar')
                        ->width(50)
                        ->height(50)
                        ->defaultImageUrl(
                            fn (Review $record): string => $record->participant->getProfilePicture()
                        )
                        ->extraCellAttributes([
                            'style' => 'width: 1px',
                        ])
                        ->circular(),
                    TextColumn::make('participant.fullName')
                        ->formatStateUsing(function (Review $record) {
                            if ($record->status == ReviewerStatus::CANCELED) {
                                return $record->participant->fullName . " (Canceled)";
                            }
                            return $record->participant->fullName;
                        })
                        ->color(
                            fn (Review $record): string => $record->status == ReviewerStatus::CANCELED ? 'danger' : 'primary'
                        )
                        ->description(function (Review $record): string {
                            return $record->participant->email;
                        }),
                    TextColumn::make('recommendation')
                        ->badge()
                        ->formatStateUsing(function ($state) {
                            return "Recommend " . $state;
                        })
                        ->color(
                            fn (Review $record): string => match ($record->recommendation) {
                                SubmissionStatusRecommendation::ACCEPT => 'primary',
                                SubmissionStatusRecommendation::DECLINE => 'danger',
                                default => 'warning'
                            }
                        )
                ]),

            ])
            ->actions([
                Action::make('see-reviews')
                    ->hidden(
                        //No review is need to be seen.
                        fn (Review $record): bool => is_null($record->date_completed)
                    )
                    ->modalWidth("2xl")
                    ->modalCancelActionLabel("Close")
                    ->modalSubmitAction(false)
                    ->icon("lineawesome-eye")
                    ->infolist(function (Review $record): array {
                        return [
                            TextEntry::make("Recommendation")
                                ->size('base')
                                ->badge()
                                ->color(
                                    fn (): string => match ($record->recommendation) {
                                        SubmissionStatusRecommendation::ACCEPT => 'primary',
                                        SubmissionStatusRecommendation::DECLINE => 'danger',
                                        default => 'warning'
                                    }
                                )
                                ->getStateUsing(fn (): string => $record->recommendation),
                            TextEntry::make('Review for Author and Editor')
                                ->size('base')
                                ->color("gray")
                                ->html()
                                ->getStateUsing(fn (): string => $record->review_author_editor),
                            TextEntry::make('Review for Editor')
                                ->size('base')
                                ->color("gray")
                                ->html()
                                ->getStateUsing(fn (): string => $record->review_editor),
                            LivewireEntry::make('reviewer-files')
                                ->livewire(ReviewerFiles::class, [
                                    'record' => $record,
                                ])
                                ->lazy(),
                            // BladeEntry::make('Recommendation')
                            //     ->blade()
                        ];
                    }),
                ActionGroup::make([
                    Action::make('edit-reviewer')
                        ->modalWidth("2xl")
                        ->icon("iconpark-edit")
                        ->label("Edit")
                        ->mountUsing(function (Review $record, Form $form) {
                            $form->fill([
                                'participant_id' => $record->participant_id,
                                'papers' => $record->getMedia('reviewer-assigned-papers')->map(function (Media $media) {
                                    return $media->getCustomProperty('copied_from');
                                })->toArray()
                            ]);
                        })
                        ->form(static::formReviewerSchema($this, true))
                        ->successNotificationTitle("Reviewer updated")
                        ->action(function (Action $action, Review $record, array $data) {
                            $record->update([
                                'participant_id' => $data['participant_id'],
                            ]);
                            $record->getMedia('reviewer-assigned-papers')
                                ->each(
                                    fn (Media $media) => $media->delete()
                                );
                            if ($data['papers']) {
                                Media::whereIn('id', $data['papers'])
                                    ->get()
                                    ->each(function (Media $paper) use ($record) {
                                        $reviewPaper = $paper->copy($record, 'reviewer-assigned-papers');
                                        $reviewPaper->setCustomProperty('copied_from', $paper->id);
                                        $reviewPaper->save();
                                    });
                            }
                            $action->success();
                        }),
                    Action::make("email-reviewer")
                        ->label("E-Mail Reviewer")
                        ->icon("iconpark-sendemail")
                        ->modalSubmitActionLabel("Send")
                        ->form([
                            TextInput::make('target')
                                ->formatStateUsing(function (Review $record) {
                                    return $record->participant->email;
                                })
                                ->disabled(),
                            TinyEditor::make('message')
                                ->minHeight(300)
                        ])
                        ->successNotificationTitle("E-mail sent")
                        ->action(function (Action $action, array $data, Review $record) {
                            // Contoh E-Mail
                            // Mail::raw($data['message'], function ($message) use ($record) {
                            //     $message->to($record->participant->email)
                            //         ->subject("Subject");
                            // });
                            $action->success();
                        }),
                    Action::make("cancel-reviewer")
                        ->color('danger')
                        ->icon("iconpark-deletethree-o")
                        ->label("Cancel Reviewer")
                        ->hidden(fn (Review $record) => $record->status == ReviewerStatus::CANCELED)
                        ->successNotificationTitle("Reviewer canceled")
                        ->modalWidth("2xl")
                        ->form([
                            Checkbox::make('do-not-notify-cancelation')
                                ->reactive()
                                ->label("Don't Send Notification")
                                ->columnSpanFull(),
                            TinyEditor::make('message')
                                ->minHeight(300)
                                ->hidden(fn (Get $get) => $get('do-not-notify-cancelation'))
                                ->columnSpanFull(),
                        ])
                        ->action(function (Action $action, Review $record) {
                            $record->update([
                                'status' => ReviewerStatus::CANCELED
                            ]);
                            $action->success();
                        }),
                    Action::make("reinstate-reviewer")
                        ->color('primary')
                        ->modalWidth("2xl")
                        ->icon("iconpark-deletethree-o")
                        ->hidden(
                            fn (Review $record) => $record->status != ReviewerStatus::CANCELED
                        )
                        ->label("Reinstate Reviewer")
                        ->successNotificationTitle("Reviewer Reinstated")
                        ->form([
                            Checkbox::make('do-not-notify-reinstatement')
                                ->reactive()
                                ->label("Don't Send Notification")
                                ->columnSpanFull(),
                            TinyEditor::make('message')
                                ->minHeight(300)
                                ->hidden(fn (Get $get) => $get('do-not-notify-reinstatement'))
                                ->columnSpanFull(),
                        ])
                        ->action(function (Action $action, Review $record) {
                            $record->update([
                                'status' => ReviewerStatus::PENDING
                            ]);
                            $action->success();
                        }),
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
                ])
            ])
            ->heading("Reviewers")
            ->headerActions([
                Action::make('add-reviewer')
                    ->icon("iconpark-adduser-o")
                    ->label("Reviewer")
                    ->modalHeading("Assign Reviewer")
                    ->modalWidth("2xl")
                    ->authorize('Review:create')
                    ->form([
                        ...static::formReviewerSchema($this),
                        Fieldset::make("Notification")
                            ->schema([
                                Checkbox::make('no-invitation-notification')
                                    ->reactive()
                                    ->label("Don't send Notification")
                                    ->columnSpanFull(),
                                TinyEditor::make('reviewer-invitation-message')
                                    ->minHeight(300)
                                    ->hidden(
                                        fn (Get $get): bool => $get('no-invitation-notification') ?? false
                                    )
                                    ->label("Reviewer invitation message")
                                    ->columnSpanFull(),
                            ])
                    ])
                    ->action(function (Action $action, array $data) {
                        if ($this->record->reviews()->where('participant_id', $data['participant_id'])->exists()) {
                            $action->failureNotificationTitle("Reviewer already assigned");
                            $action->failure();
                            return;
                        }
                        $reviewAssignment = $this->record->reviews()
                            ->create([
                                'participant_id' => $data['participant_id'],
                                'date_assigned' => now(),
                            ])
                            ->first();

                        foreach ($data['papers'] as $mediaId) {
                            $reviewAssignment->assignedFiles()
                                ->create([
                                    'media_id' => $mediaId,
                                ]);
                        }
                    })
            ]);
    }
    public function render()
    {
        return view('panel.livewire.submissions.components.reviewer-list');
    }
}
