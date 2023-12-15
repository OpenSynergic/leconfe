<?php

namespace App\Models\Enums;

use App\Models\Enums\Concern\UsefulEnums;
use Filament\Support\Contracts\HasLabel;

enum SubmissionStage: string implements HasLabel
{
    use UsefulEnums;

    case Wizard = 'Wizard';
    case CallforAbstract = 'Call for Abstract';
    case Payment = 'Payment';
    case PeerReview = 'Peer Review';
    case Editing = 'Editing';
    case Proceeding = 'Proceeding';

    public function getLabel(): ?string
    {
        return $this->name;
    }
}
