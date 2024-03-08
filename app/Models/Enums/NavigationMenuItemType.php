<?php

namespace App\Models\Enums;

use App\Models\Enums\Concern\UsefulEnums;
use Closure;
use Filament\Support\Contracts\HasLabel;

enum NavigationMenuItemType: string implements HasLabel
{
    use UsefulEnums;

    case RemoteURL      = 'Remote URL';
    case Home           = 'Home';
    case About          = 'About';
    case Announcement   = 'Announcement';
    case Login          = 'Login';
    case Register       = 'Register';
    case Proceeding     = 'Proceeding';

    public function getLabel(): ?string
    {
        return $this->name;
    }
    
    public static function getOptions(): array
    {
        return static::array();
    }
}