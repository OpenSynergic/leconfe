<?php

namespace App\Models\Enums;

use App\Models\Enums\Concern\UsefulEnums;
use Filament\Support\Contracts\HasLabel;

enum PaymentType: string 
{
    use UsefulEnums;

    case Submission = 'Submission';
    case Participant = 'Participant';
}
