<?php

namespace App\Panel\Livewire\Submissions;

use App\Actions\Submissions\SubmissionUpdateAction;
use App\Infolists\Components\LivewireEntry;
use App\Models\Enums\SubmissionStatus;
use App\Models\Submission;
use App\Panel\Livewire\Submissions\SubmissionDetail\Discussions;
use App\Panel\Livewire\Tables\Submissions\SubmissionFilesTable;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Tables\Table;
use Livewire\Component;

class CallforAbstract extends Component implements HasForms, HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;

    public Submission $submission;

    public function mount(Submission $submission)
    {
        $this->submission = $submission;
    }

    public function declineAction()
    {
        return Action::make('decline')
            ->outlined()
            ->color("danger")
            ->extraAttributes(['class' => 'w-full'], true)
            ->icon("lineawesome-times-circle-solid")
            ->requiresConfirmation();
    }

    public function acceptAction()
    {
        return Action::make('accept')
            ->outlined()
            ->record($this->submission)
            ->successNotificationTitle("Accepted")
            ->extraAttributes(['class' => 'w-full'])
            ->icon("lineawesome-check-circle-solid")
            ->action(function (Action $action) {
                SubmissionUpdateAction::run([
                    'status' => SubmissionStatus::Accepted
                ], $this->submission);
                $action->success();
            })
            ->requiresConfirmation();
    }


    public function render()
    {
        return view('panel.livewire.submissions.call-for-abstract');
    }
}
