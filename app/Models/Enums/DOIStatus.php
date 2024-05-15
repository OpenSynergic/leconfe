<?php

namespace App\Models\Enums;

use App\Models\Enums\Concern\UsefulEnums;
use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum DOIStatus: int implements HasColor, HasLabel
{
    use UsefulEnums;

    case Unregistered = 1;
    case Submitted = 2;
    case Registered = 3;
    case Error = 4;
    case Stale = 5;

    public function getLabel(): ?string
    {
        return $this->name;
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Unregistered => 'gray',
            self::Submitted => 'info',
            self::Registered => 'success',
            self::Error => 'danger',
            self::Stale => 'warning',
        };
    }


}
