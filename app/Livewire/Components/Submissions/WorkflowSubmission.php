<?php

namespace App\Livewire\Components\Submissions;

use App\Models\Submission;
use Livewire\Component;

class WorkflowSubmission extends Component
{
    public Submission $record;

    public function render()
    {
        return view('livewire.components.submissions.workflow-submission');
    }
}
