<?php

namespace App\Models\Enums;

use App\Models\Enums\Concern\UsefulEnums;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum SerieState: int implements HasColor, HasLabel
{
    use UsefulEnums;

    case Draft      = 1;
    case Published  = 2;
    case Current    = 3;
    case Archived   = 4;


    public function getLabel(): ?string
    {
        return $this->name;
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Published => 'grenn',
            self::Current => 'primary',
            self::Archived => 'warning',
        };
    }
}
