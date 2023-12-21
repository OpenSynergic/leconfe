<?php

namespace App\Models\Enums;

use App\Models\Enums\Concern\UsefulEnums;
use Filament\Support\Contracts\HasLabel;

enum PaymentState: string implements HasLabel
{
    use UsefulEnums;

    case Unpaid = 'Unpaid';
    case Processing = 'Processing';
    case Waived = 'Waived';
    case Paid = 'Paid';

    public function getLabel(): ?string
    {
        return $this->name;
    }
}
