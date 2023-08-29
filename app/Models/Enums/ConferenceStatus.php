<?php

namespace App\Models\Enums;

use App\Models\Enums\Concern\UsefulEnums;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ConferenceStatus: string implements HasLabel, HasColor
{
    use UsefulEnums;

    case Current = 'Current';
    case Archived = 'Archived';
    case Upcoming = 'Upcoming';

    public function getLabel(): ?string
    {
        return $this->name;
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Current => 'primary',
            self::Archived => 'gray',
            self::Upcoming => 'info',
        };
    }
}
