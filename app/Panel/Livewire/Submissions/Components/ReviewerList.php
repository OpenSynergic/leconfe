<?php

namespace App\Panel\Livewire\Submissions\Components;

use App\Constants\ReviewerStatus;
use App\Constants\SubmissionFileCategory;
use App\Constants\SubmissionStatusRecommendation;
use App\Infolists\Components\LivewireEntry;
use App\Mail\Templates\ReviewerCancelationMail;
use App\Mail\Templates\ReviewerInvitationMail;
use App\Models\Enums\SubmissionStage;
use App\Models\Enums\SubmissionStatus;
use App\Models\Enums\UserRole;
use App\Models\MailTemplate;
use App\Models\Media;
use App\Models\Review;
use App\Models\ReviewerAssignedFile;
use App\Models\Role;
use App\Models\Submission;
use App\Models\SubmissionFile;
use App\Models\SubmissionFileType;
use App\Models\User;
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
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;
use STS\FilamentImpersonate\Tables\Actions\Impersonate;

class ReviewerList extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public Submission $record;

    public Role $reviewerRole;

    public function mount(Submission $record)
    {
        $this->reviewerRole = Role::where('name', UserRole::Reviewer->value)->first();
    }

    private static function formReviewerSchema(ReviewerList $component, bool $editMode = false): array
    {
        return [
            Select::make('user_id')
                ->label("Reviewer")
                ->placeholder("Select a reviewer")
                ->allowHtml()
                ->preload()
                ->required()
                ->searchable()
                ->options(function ($state) use ($component, $editMode): array {
                    return User::with('roles')
                        ->whereHas('roles', function (Builder $query) use ($component) {
                            $query->where('name', $component->reviewerRole->name);
                        })
                        ->when($editMode, function ($query) use ($component, $state) {
                            $query->whereNotIn(
                                'id',
                                $component->record->reviews()
                                    ->where('user_id', '!=', $state)
                                    ->get()
                                    ->pluck('user_id')
                                    ->toArray()
                            );
                        })
                        ->when(!$editMode, function ($query) use ($component) {
                            $query->whereNotIn(
                                'id',
                                $component->record->reviews()
                                    ->get()
                                    ->pluck('user_id')
                                    ->toArray()
                            );
                        })
                        ->get()
                        ->mapWithKeys(function (User $user) {
                            return [$user->getKey() => static::renderSelectParticipant($user)];
                        })
                        ->toArray();
                }),
            CheckboxList::make('papers')
                ->label("Files to be reviewed")
                ->hidden(
                    !$component->record->getMedia(SubmissionFileCategory::PAPER_FILES)->count()
                )
                ->options(function () use ($component) {
                    return $component->record
                        ->submissionFiles()
                        ->where('category', SubmissionFileCategory::PAPER_FILES)
                        ->get()
                        ->mapWithKeys(function (SubmissionFile $paper) {
                            return [
                                $paper->getKey() => Action::make($paper->media->file_name)
                                    ->label($paper->media->file_name)
                                    ->url(function () use ($paper) {
                                        return route('private.files', ['uuid' => $paper->media->uuid]);
                                    })
                                    ->link()
                            ];
                        });
                })
                ->descriptions(function () use ($component) {
                    return $component->record
                        ->submissionFiles()
                        ->where('category', SubmissionFileCategory::PAPER_FILES)
                        ->get()
                        ->mapWithKeys(function (SubmissionFile $paper) {
                            return [$paper->getKey() => $paper->type->name];
                        });
                }),
        ];
    }

    public static function renderSelectParticipant(User $user): string
    {
        return view('forms.select-participant', ['participant' => $user])->render();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn (): Builder => $this->record->reviews()->getQuery()
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
                            fn (Review $record): string => $record->user->getFilamentAvatarUrl()
                        )
                        ->extraCellAttributes([
                            'style' => 'width: 1px',
                        ])
                        ->circular(),
                    Stack::make([
                        TextColumn::make('user.fullName')
                            ->color(
                                fn (Review $record): string => $record->status == ReviewerStatus::CANCELED ? 'danger' : 'primary'
                            )
                            ->description(
                                fn (Review $record): string => $record->user->email
                            ),
                        TextColumn::make('status')
                            ->extraAttributes(['class' => 'mt-2'])
                            ->color(function ($state) {
                                return match ($state) {
                                    ReviewerStatus::PENDING => 'warning',
                                    ReviewerStatus::CANCELED => 'danger',
                                    ReviewerStatus::ACCEPTED => 'success',
                                    default => 'primary'
                                };
                            })
                            ->badge(),
                    ]),
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
                                ->getStateUsing(fn (): ?string => $record->review_author_editor),
                            TextEntry::make('Review for Editor')
                                ->hidden(
                                    fn (): bool => $this->record->user->getKey() == auth()->id()
                                )
                                ->size('base')
                                ->color("gray")
                                ->html()
                                ->getStateUsing(fn (): ?string => $record->review_editor),
                            LivewireEntry::make('reviewer-files')
                                ->livewire(Files\ReviewerFiles::class, [
                                    'submission' => $record->submission,
                                    'viewOnly' => true
                                ])
                                ->lazy(),
                        ];
                    }),
                ActionGroup::make([
                    Action::make('edit-reviewer')
                        ->visible(
                            fn (): bool => $this->record->status == SubmissionStatus::OnReview
                        )
                        ->authorize('Submission:editReviewer')
                        ->modalWidth("2xl")
                        ->icon("iconpark-edit")
                        ->label("Edit")
                        ->mountUsing(function (Review $record, Form $form) {
                            $form->fill([
                                'user_id' => $record->user_id,
                                'papers' => $record->assignedFiles()->with('submissionFile')
                                    ->get()
                                    ->pluck('submission_file_id')
                                    ->toArray()
                            ]);
                        })
                        ->form(static::formReviewerSchema($this, true))
                        ->successNotificationTitle("Reviewer updated")
                        ->action(function (Action $action, Review $record, array $data) {
                            $record->update([
                                'user_id' => $data['user_id'],
                            ]);

                            $record->assignedFiles()->get()->each(
                                fn (ReviewerAssignedFile $file) => $file->delete()
                            );

                            if (isset($data['papers'])) {
                                collect($data['papers'])
                                    ->each(function (int $submisionFileId) use ($record) {
                                        $record->assignedFiles()->create([
                                            'submission_file_id' => $submisionFileId
                                        ]);
                                    });
                            }
                            $action->success();
                        }),
                    Action::make("email-reviewer")
                        ->authorize('Submission:emailReviewer')
                        ->label("E-Mail Reviewer")
                        ->icon("iconpark-sendemail")
                        ->modalSubmitActionLabel("Send")
                        ->mountUsing(function (Form $form, Review $record) {
                            $form->fill([
                                'email' => $record->user->email,
                                'subject' => 'Notification for you'
                            ]);
                        })
                        ->form([
                            TextInput::make('email')
                                ->dehydrated()
                                ->disabled(),
                            TextInput::make('subject')
                                ->required(),
                            TinyEditor::make('message')
                                ->minHeight(300)
                        ])
                        ->successNotificationTitle("E-mail sent")
                        ->action(function (Action $action, Review $record, array $data) {
                            Mail::send([], [], function (Message $message) use ($record, $data) {
                                $message->to($record->user->email)
                                    ->subject($data['subject'])
                                    ->html($data['message']);
                            });
                            $action->success();
                        }),
                    Action::make("cancel-reviewer")
                        ->color('danger')
                        ->icon("iconpark-deletethree-o")
                        ->label("Cancel Reviewer")
                        ->hidden(
                            fn (Review $record) => $record->status == ReviewerStatus::CANCELED || $record->confirmed()
                        )
                        ->successNotificationTitle("Reviewer canceled")
                        ->modalWidth("2xl")
                        ->mountUsing(function (Form $form, Review $record) {
                            $mailTemplate = MailTemplate::where('mailable', ReviewerCancelationMail::class)->first();
                            $form->fill([
                                'email' => $record->user->email,
                                'subject' => $mailTemplate ? $mailTemplate->subject : '',
                                'message' => $mailTemplate ? $mailTemplate->html_template : '',
                            ]);
                        })
                        ->form([
                            Fieldset::make('Notification')
                                ->columns(1)
                                ->schema([
                                    TextInput::make('email')
                                        ->disabled()
                                        ->hidden(fn (Get $get) => $get('do-not-notify-cancelation'))
                                        ->dehydrated(),
                                    TextInput::make('subject')
                                        ->hidden(fn (Get $get) => $get('do-not-notify-cancelation'))
                                        ->required()
                                        ->columnSpanFull(),
                                    TinyEditor::make('message')
                                        ->minHeight(300)
                                        ->hidden(fn (Get $get) => $get('do-not-notify-cancelation'))
                                        ->columnSpanFull(),
                                    Checkbox::make('do-not-notify-cancelation')
                                        ->reactive()
                                        ->label("Don't Send Notification")
                                        ->columnSpanFull(),
                                ])
                        ])
                        ->action(function (Action $action, Review $record, array $data) {
                            $record->update([
                                'status' => ReviewerStatus::CANCELED
                            ]);

                            if (!$data['do-not-notify-cancelation']) {
                                try {
                                    Mail::to($record->user->email)
                                        ->send(
                                            (new ReviewerCancelationMail($record))
                                                ->subjectUsing($data['subject'])
                                                ->contentUsing($data['message'])
                                        );
                                } catch (\Exception $e) {
                                    $action->failureNotificationTitle("The email notification was not delivered.");
                                    $action->failure();
                                }
                            }

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
                                ->label("Don't Send Notification")
                                ->columnSpanFull(),
                            TinyEditor::make('message')
                                ->minHeight(300)
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
                            fn (Model $record): bool => $record->user->email !== auth()->user()->email && auth()->user()->canImpersonate()
                        )
                        ->label("Login as")
                        ->icon("iconpark-login")
                        ->color('primary')
                        ->redirectTo('panel')
                        ->action(function (Model $record, Impersonate $action) {
                            $user = User::where('email', $record->user->email)->first();
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
                    ->mountUsing(function (Form $form): void {
                        $mailTemplate = MailTemplate::where('mailable', ReviewerInvitationMail::class)->first();
                        $form->fill([
                            'subject' => $mailTemplate ? $mailTemplate->subject : '',
                            'message' => $mailTemplate ? $mailTemplate->html_template : '',
                        ]);
                    })
                    ->visible(
                        fn (): bool => $this->record->status == SubmissionStatus::OnReview
                    )
                    // ->hidden(
                    //     fn (): bool => $this->record->isDeclined() || $this->record->stage == SubmissionStage::Editing || $this->record->isPublished()
                    // )
                    ->icon("iconpark-adduser-o")
                    ->label("Reviewer")
                    ->modalHeading("Assign Reviewer")
                    ->modalWidth("2xl")
                    ->authorize('Submission:assignReviewer')
                    ->form([
                        ...static::formReviewerSchema($this),
                        Fieldset::make("Notification")
                            ->schema([
                                TextInput::make('subject')
                                    ->columnSpanFull(),
                                TinyEditor::make('message')
                                    ->minHeight(300)
                                    ->label("Reviewer invitation message")
                                    ->columnSpanFull(),
                                Checkbox::make('no-invitation-notification')
                                    ->label("Don't Send Notification")
                                    ->columnSpanFull(),
                            ])
                    ])
                    ->action(function (Action $action, array $data) {
                        if ($this->record->reviews()->where('user_id', $data['user_id'])->exists()) {
                            $action->failureNotificationTitle("Reviewer already assigned");
                            $action->failure();
                            return;
                        }
                        $reviewAssignment = $this->record->reviews()
                            ->create([
                                'user_id' => $data['user_id'],
                                'date_assigned' => now(),
                            ])
                            ->first();

                        if (isset($data['papers'])) {
                            foreach ($data['papers'] as $submissionFileId) {
                                $submissionFile = SubmissionFile::find($submissionFileId);
                                $reviewAssignment->assignedFiles()
                                    ->create([
                                        'submission_file_id' => $submissionFile->getKey(),
                                        'media_id' => $submissionFile->media->getKey(),
                                    ]);
                            }
                        }

                        if (!$data['no-invitation-notification']) {
                            try {
                                Mail::to($reviewAssignment->user->email)
                                    ->send(
                                        (new ReviewerInvitationMail($reviewAssignment))
                                            ->subjectUsing($data['subject'])
                                            ->contentUsing($data['message'])
                                    );
                            } catch (\Exception $e) {
                                $action->failureNotificationTitle("The email notification was not delivered.");
                                $action->failure();
                            }
                        }
                    })
            ]);
    }
    public function render()
    {
        return view('panel.livewire.submissions.components.reviewer-list');
    }
}
