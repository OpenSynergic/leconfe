<?php

namespace App\Panel\Livewire\Submissions\Components\Files;

use App\Constants\SubmissionFileCategory;
use App\Models\Enums\SubmissionStage;
use Illuminate\Contracts\Database\Eloquent\Builder;

class SupplementaryFiles extends SubmissionFilesTable
{
    protected ?string $category = SubmissionFileCategory::SUPPLEMENTARY_FILES;

    protected string $tableHeading = "Supplementary Files";

    protected $listeners = [
        'refreshSupplementaryFiles' => '$refresh'
    ];

    public function isViewOnly(): bool
    {
        if ($this->viewOnly) {
            return $this->viewOnly;
        }
        return $this->submission->stage != SubmissionStage::Wizard && $this->submission->stage != SubmissionStage::CallforAbstract;
    }
}
