<?php

namespace App\Models\Enums;

use App\Models\Enums\Concern\UsefulEnums;
use Filament\Support\Contracts\HasLabel;

enum ContentType: string implements HasLabel
{
    use UsefulEnums;

    case Announcement = 'Announcement';
    case StaticPage = 'StaticPage';

    public function getLabel(): ?string
    {
        return $this->name;
    }
}
