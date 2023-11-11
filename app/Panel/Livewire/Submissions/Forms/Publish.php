<?php

namespace App\Panel\Livewire\Submissions\Forms;

use App\Actions\Submissions\SubmissionUpdateAction;
use App\Mail\Templates\PublishSubmissionMail;
use App\Models\Enums\SubmissionStage;
use App\Models\Enums\SubmissionStatus;
use App\Models\MailTemplate;
use App\Models\Submission;
use App\Panel\Livewire\Workflows\Classes\StageManager;
use App\Panel\Livewire\Workflows\Concerns\InteractWithTenant;
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
use Filament\Forms\Get;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Illuminate\Support\Facades\Mail;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

class Publish extends \Livewire\Component implements HasActions, HasForms, HasInfolists
{
    use InteractsWithForms, InteractsWithActions, InteractsWithInfolists, InteractWithTenant;

    public Submission $submission;

    public function handlePublishAction(Action $action, array $data)
    {
        SubmissionUpdateAction::run([
            'stage' => SubmissionStage::Proceeding,
            'status' => SubmissionStatus::Published
        ], $this->submission);

        if (!$data['do-not-notify-author']) {
            Mail::to($this->submission->user->email)
                ->send(new PublishSubmissionMail($this->submission, $data['subject'], $data['message']));
        }

        Mail::to($this->submission->user->email)
            ->send(new PublishSubmissionMail($this->submission));

        $action->success();
    }

    public function publishAction()
    {
        return Action::make('publishAction')
            ->disabled(
                fn (): bool => !StageManager::stage('editing')->isStageOpen()
            )
            ->authorize("Submission:publish")
            ->icon("iconpark-check")
            ->label("Send to Proceeding")
            ->successNotificationTitle("Submission published successfully")
            ->mountUsing(function (Form $form) {
                $mailTemplate = MailTemplate::where('mailable', PublishSubmissionMail::class)->first();
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
                            ->disabled()
                            ->dehydrated(),
                        TextInput::make('subject')
                            ->required(),
                        TinyEditor::make('message')
                            ->minHeight(300)
                            ->hidden(fn (Get $get) => $get('do-not-notify-author')),
                        Checkbox::make('do-not-notify-author')
                            ->reactive()
                            ->label("Don't Send Notification to Author"),
                    ])
            ])
            ->action(fn (Action $action, array $data) => $this->handlePublishAction($action, $data));
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->record($this->submission)
            ->schema([
                ShoutEntry::make('shout')
                    ->content("Please ensure that you have completed all the required fields before publishing your submission. Once published, you will not be able to edit your submission.")
            ]);
    }

    public function render()
    {
        return view('panel.livewire.submissions.forms.publish');
    }
}
