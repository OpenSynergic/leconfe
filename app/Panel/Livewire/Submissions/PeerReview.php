<?php

namespace App\Panel\Livewire\Submissions;

use App\Models\Submission;
use App\Panel\Livewire\Workflows\Concerns\InteractWithTenant;
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

    public function mount(Submission $submission)
    {
        $this->submission = $submission;
        $this->stageOpened = $this->conference->getMeta("workflow.peer-review.open", false);
    }

    public function render()
    {
        return view('panel.livewire.submissions.peer-review');
    }
}
