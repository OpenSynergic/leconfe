<?php

namespace App\Models\Enums;

use App\Models\Enums\Concern\UsefulEnums;

enum PaymentType: string
{
    use UsefulEnums;

    case Submission = 'Submission';
}
