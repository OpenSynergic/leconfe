<?php

namespace App\Panel\Conference\Livewire\Submissions;

use App\Mail\Templates\AcceptAbstractMail;
use App\Mail\Templates\DeclineAbstractMail;
use App\Models\MailTemplate;
use App\Models\Submission;
use App\Notifications\AbstractAccepted;
use App\Notifications\AbstractDeclined;
use App\Panel\Conference\Livewire\Workflows\Classes\StageManager;
use App\Panel\Conference\Livewire\Workflows\Concerns\InteractWithTenant;
use App\Panel\Conference\Resources\SubmissionResource;
use App\Repositories\Repository;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

class CallforAbstract extends Component implements HasActions, HasForms
{
    use InteractsWithActions, InteractsWithForms, InteractWithTenant;

    public Submission $submission;

    public function declineAction()
    {
        return Action::make('decline')
            ->outlined()
            ->color('danger')
            ->authorize('declineAbstract', $this->submission)
            ->modalWidth('2xl')
            ->record($this->submission)
            ->modalHeading('Confirmation')
            ->modalSubmitActionLabel('Decline')
            ->extraAttributes(['class' => 'w-full'], true)
            ->mountUsing(function (Form $form): void {
                $mailTempalte = MailTemplate::where('mailable', DeclineAbstractMail::class)->first();
                $form->fill([
                    'subject' => $mailTempalte ? $mailTempalte->subject : '',
                    'message' => $mailTempalte ? $mailTempalte->html_template : '',
                ]);
            })
            ->form([
                Fieldset::make('Notification')
                    ->columns(1)
                    ->schema([
                        TextInput::make('email')
                            ->disabled()
                            ->formatStateUsing(fn (Submission $record): string => $record->user->email),
                        TextInput::make('subject')
                            ->required(),
                        TinyEditor::make('message')
                            ->minHeight(300),
                        Checkbox::make('no-notification')
                            ->label("Don't send notification to author")
                            ->default(false),
                    ]),
            ])
            ->successNotificationTitle('Submission declined')
            ->successRedirectUrl(fn (): string => SubmissionResource::getUrl('view', ['record' => $this->submission]))
            ->action(function (Action $action, array $data) {
                Repository::submission()->getState($this->submission)->decline();

                if (!$data['no-notification']) {
                    try {
                        $this->submission->user->notify(
                            new AbstractDeclined(
                                submission: $this->submission,
                                message: $data['message'],
                                subject: $data['subject'],
                                channels: ['mail']
                            )
                        );
                    } catch (\Exception $e) {
                        $action->failureNotificationTitle('The email notification was not delivered.');
                        $action->failure();
                    }
                }

                $this->submission->user->notify(
                    new AbstractDeclined(
                        submission: $this->submission,
                        message: $data['message'],
                        subject: $data['subject'],
                        channels: ['database']
                    )
                );

                $action->success();
            })
            ->icon('lineawesome-times-circle-solid');
    }

    public function acceptAction()
    {
        return Action::make('accept')
            ->modalHeading('Confirmation')
            ->modalSubmitActionLabel('Accept')
            ->authorize('acceptAbstract', $this->submission)
            ->modalWidth('2xl')
            ->record($this->submission)
            ->successNotificationTitle('Accepted')
            ->extraAttributes(['class' => 'w-full'])
            ->icon('lineawesome-check-circle-solid')
            ->mountUsing(function (Form $form): void {
                $mailTemplate = MailTemplate::where('mailable', AcceptAbstractMail::class)->first();
                $form->fill([
                    'subject' => $mailTemplate ? $mailTemplate->subject : '',
                    'message' => $mailTemplate ? $mailTemplate->html_template : '',
                ]);
            })
            ->form([
                Fieldset::make('Notification')
                    ->columns(1)
                    ->schema([
                        /**
                         * TODO:
                         * - Need to create a function for it because it is used frequently.
                         *
                         * Something like:
                         *   UserNotificaiton::formSchema()
                         */
                        TextInput::make('email')
                            ->disabled()
                            ->formatStateUsing(fn (Submission $record): string => $record->user->email),
                        TextInput::make('subject')
                            ->required(),
                        TinyEditor::make('message')
                            ->minHeight(300)
                            ->toolbarSticky(false),
                        Checkbox::make('no-notification')
                            ->label("Don't send notification to author")
                            ->default(false),
                    ]),
            ])
            ->action(
                function (Action $action, array $data) {
                    try {
                        Repository::submission()->getState($this->submission)->acceptAbstract();

                        if (!$data['no-notification']) {
                            try {
                                $this->submission->user
                                    ->notify(
                                        new AbstractAccepted(
                                            submission: $this->submission,
                                            message: $data['message'],
                                            subject: $data['subject'],
                                            channels: ['mail']
                                        )
                                    );
                            } catch (\Exception $e) {
                                $action->failureNotificationTitle('The email notification was not delivered.');
                                $action->failure();
                            }
                        }

                        $this->submission->user
                            ->notify(
                                new AbstractAccepted(
                                    submission: $this->submission,
                                    message: $data['message'],
                                    subject: $data['subject'],
                                    channels: ['database']
                                )
                            );

                        $action->successRedirectUrl(
                            SubmissionResource::getUrl('view', [
                                'record' => $this->submission->getKey(),
                            ])
                        );

                        $action->success();
                    } catch (\Throwable $th) {
                        Log::error($th->getMessage());
                        $action->failureNotificationTitle('Failed to accept abstract');
                        $action->failure();
                    }
                }
            );
    }

    public function render()
    {
        return view('panel.conference.livewire.submissions.call-for-abstract', [
            'reviewStageOpen' => StageManager::peerReview()->isStageOpen(),
        ]);
    }
}
