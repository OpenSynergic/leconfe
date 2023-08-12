<?php

namespace App\Models\Enums;

use App\Models\Enums\Concern\UsefulEnums;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ConferenceType: string implements HasLabel, HasColor
{
    use UsefulEnums;

    case Offline = 'Offline';
    case Online = 'Online';
    case Hybrid = 'Hybrid';

    public function getLabel(): ?string
    {
        return $this->name;
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Offline => 'primary',
            self::Online => 'warning',
            self::Hybrid => 'success',
        };
    }
}
