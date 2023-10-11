<?php

namespace App\Models\Enums;

use App\Models\Enums\Concern\UsefulEnums;
use Filament\Support\Contracts\HasLabel;

enum SubmissionFileCategory: string implements HasLabel
{
    use UsefulEnums;

    case Files = 'submission-files';
    case Papers = 'submission-papers';
    case ReviewerAssignedPapers = "reviewer-assigned-papers";
    case ReviewerFiles = 'reviewer-files';

    public function getLabel(): ?string
    {
        return $this->name;
    }
}
