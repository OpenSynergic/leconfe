<?php

namespace App\Panel\Livewire\Submissions\Forms;

use App\Actions\Submissions\SubmissionUpdateAction;
use App\Models\Enums\SubmissionStage;
use App\Models\Enums\SubmissionStatus;
use App\Models\Submission;
use Awcodes\Shout\Components\ShoutEntry;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;

class Publish extends \Livewire\Component implements HasActions, HasForms, HasInfolists
{
    use InteractsWithForms, InteractsWithActions, InteractsWithInfolists;

    public Submission $submission;

    public function handlePulihsAction(Action $action)
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
            ->icon("iconpark-check")
            ->label("Publish")
            ->requiresConfirmation()
            ->successNotificationTitle("Submission published successfully")
            ->action(fn (Action $action) => $this->handlePulihsAction($action));
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
