<?php

namespace App\Infolists;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Concerns\HasId;
use App\Facades\Hook;
use Closure;
use Illuminate\Support\Str;

class Infolist extends Schema
{
    use HasId;

    /**
     * @param  array<Component> | Closure  $components
     */
    public function components(\Filament\Schemas\Components\Component|\Filament\Actions\Action|\Filament\Actions\ActionGroup|\Illuminate\Contracts\Support\Htmlable|Closure|array|string $components): static
    {
        if ($this->getId()) {
            Hook::call('Forms::Form::components::'.Str::camel($this->getId()), [&$components, $this]);
        }

        return parent::components($components);
    }
}
