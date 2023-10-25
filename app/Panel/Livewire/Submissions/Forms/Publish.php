<?php

namespace App\Panel\Livewire\Submissions\Forms;

use App\Actions\Submissions\SubmissionUpdateAction;
use App\Models\Enums\SubmissionStage;
use App\Models\Enums\SubmissionStatus;
use App\Models\Submission;
use App\Panel\Livewire\Workflows\Concerns\InteractWithTenant;
use Awcodes\Shout\Components\ShoutEntry;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Get;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

class Publish extends \Livewire\Component implements HasActions, HasForms, HasInfolists
{
    use InteractsWithForms, InteractsWithActions, InteractsWithInfolists, InteractWithTenant;

    public Submission $submission;

    public function handlePublishAction(Action $action)
    {
        SubmissionUpdateAction::run([
            'stage' => SubmissionStage::Proceeding,
            'status' => SubmissionStatus::Published
        ], $this->submission);

        $action->success();
    }

    public function publishAction()
    {
        return Action::make('publishAction')
            ->disabled(
                fn (): bool => !$this->conference->getMeta('workflow.editing.open', false)
            )
            ->authorize("Submission:publish")
            ->icon("iconpark-check")
            ->label("Publish")
            ->successNotificationTitle("Submission published successfully")
            ->form([
                Fieldset::make('Notification')
                    ->schema([
                        Checkbox::make('do-not-notify-author')
                            ->reactive()
                            ->label("Don't Send Notification to Author")
                            ->columnSpanFull(),
                        TinyEditor::make('message')
                            ->minHeight(300)
                            ->hidden(fn (Get $get) => $get('do-not-notify-author'))
                            ->columnSpanFull(),
                    ])
            ])
            ->action(fn (Action $action) => $this->handlePublishAction($action));
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
