<?php

namespace App\Http\Livewire\Tables;

use Livewire\Component;
use Filament\Tables;


class Sections extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    public function render()
    {
        return view('livewire.tables.sections');
    }
}
