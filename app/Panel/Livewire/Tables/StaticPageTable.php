<?php

namespace App\Panel\Livewire\Tables;

use App\Panel\Livewire\Traits\PlaceholderTrait;
use App\Schemas\StaticPageSchema;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;

class StaticPageTable extends Component implements HasTable, HasForms
{
    use InteractsWithTable, InteractsWithForms, PlaceholderTrait;

    public function render()
    {
        return view('panel.livewire.tables.table');
    }

    public function table(Table $table): Table
    {
        return StaticPageSchema::table($table);
    }
}
