<?php

namespace App\Panel\Livewire\Submissions;

use App\Models\Submission;
use App\Panel\Livewire\Workflows\Concerns\InteractWithTenant;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Livewire\Component;

class PeerReview extends Component implements HasForms, HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;
    use InteractWithTenant;

    public Submission $submission;

    public bool $stageOpened = false;

    protected $listeners = [
        'refreshPeerReview' => '$refresh'
    ];

    public function skipReviewAction()
    {
        return Action::make('skipReviewAction')
            ->label('Skip Review')
            ->icon("lineawesome-check-circle-solid")
            ->color('gray')
            ->outlined()
            ->requiresConfirmation();
    }

    public function acceptAction()
    {
        return Action::make('acceptAction')
            ->label("Accept")
            ->requiresConfirmation()
            ->outlined()
            ->action(function (Action $action) {
            });
    }

    public function requestRevisionAction()
    {
        return Action::make('requestRevisionAction')
            ->label("Request Revision")
            ->color("warning")
            ->outlined()
            ->requiresConfirmation()
            ->action(function (Action $action) {
            });
    }

    public function suggestAcceptAction()
    {
        return Action::make('suggestAcceptAction')
            ->label("Suggest Accept")
            ->outlined()
            ->requiresConfirmation()
            ->action(function (Action $action) {
            });
    }

    public function mount(Submission $submission)
    {
        $this->stageOpened = $this->conference->getMeta("workflow.peer-review.open", false);
    }

    public function render()
    {
        return view('panel.livewire.submissions.peer-review');
    }
}
