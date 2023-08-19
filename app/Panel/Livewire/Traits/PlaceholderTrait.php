<?php

namespace App\Panel\Livewire\Traits;

use Illuminate\Support\Facades\Blade;

trait PlaceholderTrait
{
    public function placeholder()
    {
        return Blade::render(<<<'Blade'
    <div>
      <x-filament::loading-indicator class="h-5 w-5" />
    </div>  
    Blade);
    }
}
