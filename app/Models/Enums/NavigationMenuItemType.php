<?php

namespace App\Models\Enums;

use App\Models\Enums\Concern\UsefulEnums;
use Closure;
use Filament\Forms\Components\TextInput;
use Filament\Support\Contracts\HasLabel;

enum NavigationMenuItemType: string implements HasLabel
{
    use UsefulEnums;

    case Empty          = 'Empty';
    case RemoteURL      = 'Remote URL';
    case Home           = 'Home';
    case About          = 'About';
    case Announcement   = 'Announcement';
    case Proceeding     = 'Proceeding';
    case Login          = 'Login';
    case Register       = 'Register';
    case Dashboard      = 'Dashboard';
    case Profile        = 'Profile';
    case Logout         = 'Logout';

    public function getLabel(): ?string
    {
        return $this->name;
    }
    
    public static function getOptions(): array
    {
        return static::array();
    }

    public function getForm() : array
    {
        return match($this){
            self::RemoteURL => [
                TextInput::make('meta.url')
                    ->label('URL')
                    ->url()
                    ->required()
                    ->placeholder('https://example.com'),
            ],
            default => [],
        };
    }

    public function isDisplayed(): bool
    {
        $isLoggedIn = auth()->check();

        return match($this){
            self::Login, self::Register => !$isLoggedIn,
            self::Logout, self::Dashboard, self::Profile  => $isLoggedIn,
            default => true,
        };
    }
}