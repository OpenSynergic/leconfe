<?php

namespace App\Panel\Conference\Livewire\Submissions;

use App\Models\Submission;

class Editing extends \Livewire\Component
{
    public Submission $submission;

    protected $listeners = [
        'refreshSubmission' => '$refresh',
    ];

    public function render()
    {
        return view('panel.conference.livewire.submissions.editing');
    }
}
