<?php

namespace App\Panel\Livewire\Submissions;

use App\Models\Submission;

class Editing extends \Livewire\Component
{
    public Submission $submission;

    public function render()
    {
        return view('panel.livewire.submissions.editing');
    }
}
