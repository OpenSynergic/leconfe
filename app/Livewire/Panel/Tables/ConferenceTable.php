<?php

namespace App\Livewire\Panel\Tables;

use App\Livewire\Panel\Traits\PlaceholderTrait;
use App\Schemas\ConferenceSchema;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;
use Illuminate\Contracts\View\View;

class ConferenceTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;
    use PlaceholderTrait;

    public function table(Table $table): Table
    {
        return ConferenceSchema::table($table);
    }

    public function render(): View
    {
        return view('livewire.panel.tables.table');
    }
}
