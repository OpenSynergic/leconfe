<?php

namespace App\Models\Enums;

use App\Models\Enums\Concern\UsefulEnums;
use Filament\Support\Contracts\HasLabel;

enum ReviewerConfirmationStatus: string implements HasLabel
{
    use UsefulEnums;

    case Waiting = 'Waiting Response';
    case Accepted = 'Accepted';
    case Declined = 'Declined';

    public function getLabel(): ?string
    {
        return $this->name;
    }
}
