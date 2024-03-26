<?php

namespace App\Models\Enums;

use App\Models\Enums\Concern\UsefulEnums;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum PresenterStatus: string implements HasColor, HasLabel
{
    use UsefulEnums;

    case Approve = 'Approve';
    case Reject = 'Reject';
    case Unchecked = 'Unchecked';

    public function getLabel(): ?string
    {
        return $this->name;
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Reject => 'danger',
            self::Approve => 'primary',
            self::Unchecked => 'gray',
            default => 'gray'
        };
    }

    public function getActionColor(): string
    {
        return match ($this) {
            self::Reject => 'danger',
            self::Approve => 'primary',
            self::Unchecked => 'primary',
            default => 'gray'
        };
    }

    public function getActionIcon(): string
    {
        return match ($this) {
            self::Reject => 'heroicon-o-x-mark',
            self::Approve => 'check-circle',
            self::Unchecked => 'heroicon-o-chevron-up-down',
            default => 'heroicon-o-chevron-up-down'
        };
    }

    public function getActionText(): string
    {
        return match ($this) {
            self::Reject => 'Rejected',
            self::Approve => 'Approved',
            self::Unchecked => 'Unchecked',
            default => 'Unchecked'
        };
    }
}
