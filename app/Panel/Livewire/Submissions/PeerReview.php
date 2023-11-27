<?php

namespace App\Panel\Livewire\Submissions;

use App\Actions\Submissions\SubmissionUpdateAction;
use App\Mail\Templates\AcceptPaperMail;
use App\Mail\Templates\DeclinePaperMail;
use App\Mail\Templates\RevisionRequestMail;
use App\Models\Enums\SubmissionStage;
use App\Models\Enums\SubmissionStatus;
use App\Models\MailTemplate;
use App\Models\Submission;
use App\Panel\Livewire\Workflows\Classes\StageManager;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Support\Colors\Color;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

class PeerReview extends Component implements HasForms, HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;
    public Submission $submission;

    public bool $stageOpened = false;

    protected $listeners = [
        'refreshPeerReview' => '$refresh'
    ];

    public function mount(Submission $submission)
    {
        $this->stageOpened = StageManager::peerReview()->isStageOpen();
    }

    public function declineSubmissionAction()
    {
        return Action::make('declineSubmissionAction')
            ->icon("lineawesome-times-solid")
            ->authorize(fn () => auth()->user()->can('declinePaper', $this->submission))
            ->label("Decline Submission")
            ->color('danger')
            ->outlined()
            ->mountUsing(function (Form $form) {
                $mailTemplate = MailTemplate::where('mailable', DeclinePaperMail::class)->first();
                $form->fill([
                    'email' => $this->submission->user->email,
                    'subject' => $mailTemplate ? $mailTemplate->subject : '',
                    'message' => $mailTemplate ? $mailTemplate->html_template : ''
                ]);
            })
            ->form([
                Fieldset::make("Notification")
                    ->columns(1)
                    ->schema([
                        TextInput::make('email')
                            ->readOnly()
                            ->dehydrated(),
                        TextInput::make('subject')
                            ->required(),
                        TinyEditor::make('message')
                            ->minHeight(300)
                            ->columnSpanFull(),
                        Checkbox::make('do-not-notify-author')
                            ->label("Don't Send Notification to Author")
                            ->columnSpanFull(),
                    ])
            ])
            ->action(function (Action $action, array $data) {
                SubmissionUpdateAction::run([
                    'revision_required' => false,
                    'status' => SubmissionStatus::Declined,
                ], $this->submission);

                if (!$data['do-not-notify-author']) {
                    try {
                        Mail::to($this->submission->user->email)
                            ->send(
                                (new DeclinePaperMail($this->submission))
                                    ->subjectUsing($data['subject'])
                                    ->contentUsing($data['message'])
                            );
                    } catch (\Exception $e) {
                        $action->failureNotificationTitle("The email notification was not delivered.");
                        $action->failure();
                    }
                }
                $action->success();
            });
    }


    public function acceptSubmissionAction()
    {
        return Action::make('acceptSubmissionAction')
            ->authorize(fn () => auth()->user()->can('acceptPaper', $this->submission))
            ->icon("lineawesome-check-circle-solid")
            ->color("primary")
            ->label("Accept Submission")
            ->modalSubmitActionLabel("Accept")
            ->mountUsing(function (Form $form) {
                $mailTemplate = MailTemplate::where('mailable', AcceptPaperMail::class)->first();
                $form->fill([
                    'email' => $this->submission->user->email,
                    'subject' => $mailTemplate ? $mailTemplate->subject : '',
                    'message' => $mailTemplate ? $mailTemplate->html_template : ''
                ]);
            })
            ->form([
                Fieldset::make('Notification')
                    ->columns(1)
                    ->schema([
                        TextInput::make('email')
                            ->readOnly()
                            ->dehydrated(),
                        TextInput::make('subject')
                            ->required(),
                        TinyEditor::make('message')
                            ->minHeight(300)
                            ->columnSpanFull(),
                        Checkbox::make('do-not-notify-author')
                            ->label("Don't Send Notification to Author")
                            ->columnSpanFull(),
                    ])
            ])
            ->action(function (Action $action, array $data) {
                SubmissionUpdateAction::run([
                    'revision_required' => false,
                    'stage' => SubmissionStage::Editing,
                    'status' => SubmissionStatus::Editing,
                ], $this->submission);

                if (!$data['do-not-notify-author']) {
                    try {
                        Mail::to($this->submission->user->email)
                            ->send(
                                (new AcceptPaperMail($this->submission))
                                    ->subjectUsing($data['subject'])
                                    ->contentUsing($data['message'])
                            );
                    } catch (\Exception $e) {
                        $action->failureNotificationTitle("The email notification was not delivered.");
                        $action->failure();
                    }
                }

                $action->success();
            });
    }

    public function requestRevisionAction()
    {
        return Action::make('requestRevisionAction')
            ->authorize('requestRevision', $this->submission)
            ->hidden(fn (): bool => $this->submission->revision_required)
            ->icon("lineawesome-list-alt-solid")
            ->outlined()
            ->color(Color::Orange)
            ->label("Request Revision")
            ->mountUsing(function (Form $form): void {
                $mailTemplate = MailTemplate::where('mailable', RevisionRequestMail::class)->first();
                $form->fill([
                    'email' => $this->submission->user->email,
                    'subject' => $mailTemplate ? $mailTemplate->subject : '',
                    'message' => $mailTemplate ? $mailTemplate->html_template : ''
                ]);
            })
            ->form([
                Fieldset::make("Notification")
                    ->columns(1)
                    ->schema([
                        TextInput::make('email')
                            ->readOnly()
                            ->dehydrated(),
                        TextInput::make('subject')
                            ->required(),
                        TinyEditor::make('message')
                            ->minHeight(300)
                            ->columnSpanFull(),
                        Checkbox::make('do-not-notify-author')
                            ->label("Don't Send Notification to Author")
                            ->columnSpanFull(),
                    ])
            ])
            ->successNotificationTitle("Revision Requested")
            ->action(function (Action $action, array $data) {
                SubmissionUpdateAction::run([
                    'revision_required' => true
                ], $this->submission);

                if (!$data['do-not-notify-author']) {
                    try {
                        Mail::to($this->submission->user->email)
                            ->send(
                                (new RevisionRequestMail($this->submission))
                                    ->subjectUsing($data['subject'])
                                    ->contentUsing($data['message'])
                            );
                    } catch (\Exception $e) {
                        $action->failureNotificationTitle("The email notification was not delivered.");
                        $action->failure();
                    }
                }

                $action->success();
            });
    }

    public function skipReviewAction()
    {
        return Action::make('skipReviewAction')
            ->label('Skip Review')
            ->icon("lineawesome-check-circle-solid")
            ->color('gray')
            ->outlined()
            ->successNotificationTitle("Review Skipped")
            ->action(function (Action $action) {
                SubmissionUpdateAction::run([
                    'skipped_review' => true,
                    'revision_required' => false,
                    'status' => SubmissionStatus::Editing,
                    'stage' => SubmissionStage::Editing,
                ], $this->submission);
                $action->success();
            })
            ->requiresConfirmation();
    }

    public function render()
    {
        return view('panel.livewire.submissions.peer-review');
    }
}
