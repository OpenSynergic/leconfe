<?php

namespace App\Models\Enums;

use App\Models\Enums\Concern\UsefulEnums;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ConferenceStatus: string  implements HasLabel, HasColor
{
    use UsefulEnums;

    case Active = 'Active';
    case Archived = 'Archived';

    public function getLabel(): ?string
    {
        return $this->name;
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Active => 'primary',
            self::Archived => 'gray',
        };
    }
}
