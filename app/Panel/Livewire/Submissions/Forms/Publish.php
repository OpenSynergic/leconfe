<?php

namespace App\Panel\Livewire\Submissions\Forms;

use App\Mail\Templates\PublishSubmissionMail;
use App\Models\Enums\SubmissionStage;
use App\Models\Enums\SubmissionStatus;
use App\Models\MailTemplate;
use App\Models\Submission;
use App\Panel\Livewire\Workflows\Classes\StageManager;
use App\Panel\Livewire\Workflows\Concerns\InteractWithTenant;
use App\Panel\Resources\SubmissionResource;
use Awcodes\Shout\Components\ShoutEntry;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\HtmlString;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

class Publish extends \Livewire\Component implements HasActions, HasForms, HasInfolists
{
    use InteractsWithActions, InteractsWithForms, InteractsWithInfolists, InteractWithTenant;

    public Submission $submission;

    public function handlePublishAction(Action $action, array $data)
    {
        $this->submission->state()->publish();

        if (! $data['do-not-notify-author']) {
            try {
                Mail::to($this->submission->user->email)
                    ->send(
                        (new PublishSubmissionMail($this->submission))
                            ->subjectUsing($data['subject'])
                            ->contentUsing($data['message'])
                    );
            } catch (\Exception $e) {
                $action->failureNotificationTitle('Failed to send notification to author');
                $action->failure();
            }
        }

        /**
         * Using this way because,
         * can't refresh the component
         */
        $action->successRedirectUrl(
            SubmissionResource::getUrl('view', [
                'record' => $this->submission->id,
            ])
        );

        // The subheading has been updated, but the publication's content will be lost after this dispatched.
        // $this->dispatch('refreshSubHeading');

        $action->success();
    }

    public function publishAction()
    {
        return Action::make('publishAction')
            ->disabled(
                fn (): bool => ! StageManager::editing()->isStageOpen()
            )
            ->authorize('publish', $this->submission)
            ->icon('iconpark-check')
            ->label('Send to Proceeding')
            ->when(
                fn () => $this->submission->hasPaymentProcess() && ! $this->submission->payment?->isCompleted(),
                fn (Action $action): Action => $action
                    ->modalContent(new HtmlString(<<<'HTML'
                        <p>Submission fee has not been paid, please notify the author.</p>
                    HTML))
                    ->modalWidth('xl')
                    ->modalSubmitAction(false)
            )
            ->when(
                // true,
                fn () => ! $this->submission->hasPaymentProcess() || $this->submission->payment?->isCompleted(),
                fn (Action $action): Action => $action
                    ->successNotificationTitle('Submission published successfully')
                    ->mountUsing(function (Form $form) {
                        $mailTemplate = MailTemplate::where('mailable', PublishSubmissionMail::class)->first();
                        $form->fill([
                            'email' => $this->submission->user->email,
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
                                    ->dehydrated(),
                                TextInput::make('subject')
                                    ->required(),
                                TinyEditor::make('message')
                                    ->minHeight(300),
                                Checkbox::make('do-not-notify-author')
                                    ->label("Don't Send Notification to Author"),
                            ]),
                    ])
                    ->action(fn (Action $action, array $data) => $this->handlePublishAction($action, $data))
            );
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->record($this->submission)
            ->schema([
                ShoutEntry::make('shout')
                    ->color(function (): string {
                        if (! StageManager::editing()->isStageOpen()) {
                            return 'warning';
                        }

                        if ($this->submission->status == SubmissionStatus::Published) {
                            return 'primary';
                        }

                        if ($this->submission->stage == SubmissionStatus::Editing) {
                            return 'info';
                        }

                        return 'warning';
                    })
                    ->content(function (): string {
                        if (! StageManager::editing()->isStageOpen()) {
                            return 'You are unable to publish this submission because the editing stage is not yet open.';
                        }

                        if ($this->submission->status == SubmissionStatus::Published) {
                            return 'This submission has been published.';
                        }

                        if ($this->submission->stage == SubmissionStage::Editing) {
                            return 'Please ensure that you have completed all the required fields before publishing your submission. Once published, you will not be able to edit your submission.';
                        }

                        return 'This submission is not in the editing stage.';
                    }),
            ]);
    }

    public function render()
    {
        return view('panel.livewire.submissions.forms.publish');
    }
}
