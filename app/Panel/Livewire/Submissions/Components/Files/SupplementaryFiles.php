<?php

namespace App\Panel\Livewire\Submissions\Components\Files;

use App\Constants\SubmissionFileCategory;
use Illuminate\Contracts\Database\Eloquent\Builder;

class SupplementaryFiles extends SubmissionFilesTable
{
    public string $category = SubmissionFileCategory::SUPPLEMENTARY_FILES;

    protected $listeners = [
        'refreshSupplementaryFiles' => '$refresh'
    ];

    public function tableHeading(): string
    {
        return "Supplementary Files";
    }
}
