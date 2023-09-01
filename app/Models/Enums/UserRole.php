<?php

namespace App\Models\Enums;

use App\Models\Enums\Concern\UsefulEnums;
use Filament\Support\Contracts\HasLabel;

enum UserRole: string implements HasLabel
{
    use UsefulEnums;

    case Admin = 'Admin';
    case ConferenceManager = 'Conference Manager';
    case Director = 'Director';
    case TrackDirector = 'Track Director';
    case Reviewer = 'Reviewer';
    case Author = 'Author';
    case Participant = 'Participant';

    public function getLabel(): ?string
    {
        return $this->name;
    }
}
