<?php

namespace App\Livewire\Panel\Components\Submissions;

use App\Models\Submission;
use Livewire\Component;

class WorkflowSubmission extends Component
{
    public Submission $record;

    public function render()
    {
        return view('livewire.panel.components.submissions.workflow-submission');
    }
}
