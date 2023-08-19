<?php

namespace App\Panel\Livewire\Components\Submissions;

use App\Models\Submission;
use Livewire\Component;

class WorkflowSubmission extends Component
{
    public Submission $record;

    public function render()
    {
        return view('panel.livewire.components.submissions.workflow-submission');
    }
}
