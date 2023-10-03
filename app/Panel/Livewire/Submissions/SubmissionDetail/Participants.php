<?php

namespace App\Panel\LiveWire\Submissions\SubmissionDetail;

use App\Models\Submission;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;

class Participants extends Component
{
    // use InteractsWithForms;
    // use InteractsWithTable;

    public Submission $submission;

    public array $comments = [];

    public function mount(Submission $submission)
    {
        $this->submission = $submission;
    }


    public function render()
    {
        return view('panel.livewire.submissions.submission-detail.participants');
    }
}
