<?php

namespace App\Models\Enums;

use App\Models\Enums\Concern\UsefulEnums;
use Filament\Support\Contracts\HasLabel;

enum SidebarPosition: string implements HasLabel
{
    use UsefulEnums;

    case Left = 'left';
    case Right = 'right';
    case Both = 'both';
    case None = 'none';

    public function getLabel(): ?string
    {
        return $this->name;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
