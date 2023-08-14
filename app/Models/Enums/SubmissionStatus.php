<?php

namespace App\Models\Enums;

use App\Models\Enums\Concern\UsefulEnums;
use Filament\Support\Contracts\HasLabel;

enum SubmissionStatus: string implements HasLabel
{
    use UsefulEnums;

    case Wizard = 'Wizard';
    case New = 'New';
    case UnderReview = 'UnderReview';
    case Accepted = 'Accepted';
    case Published = 'Published';
    case Declined = 'Declined';

    public function getLabel(): ?string
    {
        return $this->name;
    }
}