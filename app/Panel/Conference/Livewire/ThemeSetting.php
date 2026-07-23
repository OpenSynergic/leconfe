<?php

namespace App\Panel\Conference\Livewire;

use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;
use App\Panel\Administration\Livewire\ThemeSetting as BaseThemeSetting;

class ThemeSetting extends BaseThemeSetting implements HasActions
{
    use InteractsWithActions;
}
