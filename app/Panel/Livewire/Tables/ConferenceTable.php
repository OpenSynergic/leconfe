<?php

namespace App\Panel\Livewire\Tables;

use App\Panel\Livewire\Traits\PlaceholderTrait;
use App\Schemas\ConferenceSchema;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Livewire\Component;

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
        return view('panel.livewire.tables.table');
    }
}
