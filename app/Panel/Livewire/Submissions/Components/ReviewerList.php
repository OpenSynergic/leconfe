<?php

namespace App\Panel\Livewire\Submissions\Components;

use App\Models\Enums\UserRole;
use App\Models\Media;
use App\Models\Participant;
use App\Models\ParticipantPosition;
use App\Models\ReviewAssignment;
use App\Models\Submission;
use App\Models\SubmissionFileType;
use App\Models\User;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\View;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
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
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\HtmlString;
use Livewire\Component;
use Spatie\MediaLibrary\Support\UrlGenerator\UrlGeneratorFactory;
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
                ->unique(
                    table: 'review_assignments',
                    column: 'participant_id',
                    ignoreRecord: $editMode
                )
                ->searchable()
                ->options(function () use ($component): array {
                    return Participant::with('positions')
                        ->whereHas('positions', function ($query) use ($component) {
                            $query->where('name', $component->reviewerPosition->name);
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
                    fn (): bool => !$component->record->papers()->exists()
                )
                ->options(function () use ($component) {
                    return $component->record
                        ->papers()
                        ->get()
                        ->mapWithKeys(function (Media $paper) {
                            return [
                                $paper->getKey() => Action::make($paper->file_name)
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
            ->query(function (): Builder {
                return $this->record->reviewAssignments()->getQuery();
            })
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
                        ->formatStateUsing(function (ReviewAssignment $record) {
                            if ($record->canceled) {
                                return $record->participant->fullName . " (Canceled)";
                            }
                            return $record->participant->fullName;
                        })
                        ->color(
                            fn (ReviewAssignment $record): string => $record->canceled ? 'danger' : 'primary'
                        )
                        ->description(function (ReviewAssignment $record): string {
                            return $record->participant->email;
                        })
                ]),
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('edit-reviewer')
                        ->modalWidth("2xl")
                        ->icon("iconpark-edit")
                        ->label("Edit")
                        ->mountUsing(function (ReviewAssignment $record, Form $form) {
                            $form->fill([
                                'participant_id' => $record->participant_id,
                                'papers' => $record->getMedia('reviewer-assigned-papers')->map(function (Media $media) {
                                    return $media->getCustomProperty('copied_from');
                                })->toArray()
                            ]);
                        })
                        ->form(static::formReviewerSchema($this, true))
                        ->successNotificationTitle("Reviewer updated")
                        ->action(function (Action $action, ReviewAssignment $record, array $data) {
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
                                ->formatStateUsing(function (ReviewAssignment $record) {
                                    return $record->participant->email;
                                })
                                ->disabled(),
                            RichEditor::make('message')
                        ])
                        ->successNotificationTitle("E-mail sent")
                        ->action(function (Action $action, array $data, ReviewAssignment $record) {
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
                        ->hidden(fn (ReviewAssignment $record) => $record->canceled)
                        ->successNotificationTitle("Reviewer canceled")
                        ->modalWidth("2xl")
                        ->form([
                            Checkbox::make('do-not-notify-cancelation')
                                ->reactive()
                                ->label("Don't Send Notification")
                                ->columnSpanFull(),
                            RichEditor::make('message')
                                ->hidden(fn (Get $get) => $get('do-not-notify-cancelation'))
                                ->columnSpanFull(),
                        ])
                        ->action(function (Action $action, ReviewAssignment $record) {
                            $record->update([
                                'canceled' => true
                            ]);
                            $action->success();
                        }),
                    Action::make("reinstate-reviewer")
                        ->color('primary')
                        ->modalWidth("2xl")
                        ->icon("iconpark-deletethree-o")
                        ->hidden(fn (ReviewAssignment $record) => !$record->canceled)
                        ->label("Reinstate Reviewer")
                        ->successNotificationTitle("Reviewer Reinstated")
                        ->form([
                            Checkbox::make('do-not-notify-reinstatement')
                                ->reactive()
                                ->label("Don't Send Notification")
                                ->columnSpanFull(),
                            RichEditor::make('message')
                                ->hidden(fn (Get $get) => $get('do-not-notify-reinstatement'))
                                ->columnSpanFull(),
                        ])
                        ->action(function (Action $action, ReviewAssignment $record) {
                            $record->update([
                                'canceled' => false
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
                    ->authorize('ReviewAssignment:create')
                    ->form([
                        ...static::formReviewerSchema($this),
                        Fieldset::make("Notification")
                            ->schema([
                                Checkbox::make('send-invitation-notification')
                                    ->reactive()
                                    ->label("Send Notification")
                                    ->columnSpanFull(),
                                RichEditor::make("reviewer-invitation-message")
                                    ->visible(
                                        fn (Get $get): bool => $get('send-invitation-notification') ?? false
                                    )
                                    ->label("Reviewer invitation message")
                                    ->columnSpanFull(),
                            ])
                    ])
                    ->action(function (Action $action, array $data) {
                        if ($this->record->reviewAssignments()->where('participant_id', $data['participant_id'])->exists()) {
                            $action->failureNotificationTitle("Reviewer already assigned");
                            $action->failure();
                            return;
                        }
                        $reviewAssignment = $this->record->reviewAssignments()
                            ->create([
                                'participant_id' => $data['participant_id'],
                                'date_assigned' => now(),
                            ])
                            ->first();

                        Media::whereIn('id', $data['papers'])
                            ->get()
                            ->each(function (Media $paper) use ($reviewAssignment) {
                                $reviewPaper = $paper->copy($reviewAssignment, 'reviewer-assigned-papers', 'files');
                                $reviewPaper->setCustomProperty('copied_from', $paper->id);
                                $reviewPaper->save();
                            });
                    })
            ]);
    }
    public function render()
    {
        return view('panel.livewire.submissions.components.reviewer-list');
    }
}
