<?php

namespace App\Forms\Components;

use Closure;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Field;
use Illuminate\Contracts\Support\Htmlable;

class TagSuggestions extends CheckboxList
{
    protected string $view = 'panel.components.tag-suggestions';
}
