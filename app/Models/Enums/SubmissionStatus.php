<?php

namespace App\Models\Enums;

use App\Models\Enums\Concern\UsefulEnums;
use Filament\Support\Contracts\HasLabel;

enum SubmissionStatus: string implements HasLabel
{
    use UsefulEnums;

    case Incomplete = 'Incomplete';
    case Queued = 'Queued';
    case OnReview = 'On Review';
    case Editing = 'Editing';
    case Published = 'Published';
    case Declined = 'Declined';
    case Scheduled = 'Scheduled';
    case Withdrawn = 'Withdrawn';

    public function getLabel(): ?string
    {
        return $this->name;
    }
}
