<?php

namespace App\Models\Enums;

use App\Models\Enums\Concern\UsefulEnums;
use Filament\Support\Contracts\HasLabel;

enum SubmissionStatusRecommendation: string implements HasLabel
{
    use UsefulEnums;

    case Accept = 'Accept';
    case Decline = 'Decline';
    case RevisionRequired = "Revision Required";

    public function getLabel(): ?string
    {
        return $this->name;
    }
}
