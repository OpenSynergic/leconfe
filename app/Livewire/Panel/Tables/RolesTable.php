<?php

namespace App\Livewire\Panel\Tables;

use App\Livewire\Panel\Traits\PlaceholderTrait;
use App\Schemas\RoleSchema;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;

class RolesTable extends Component implements HasTable, HasForms
{
    use InteractsWithTable, InteractsWithForms, PlaceholderTrait;

    public function render()
    {
        return view('livewire.panel.tables.table');
    }

    public function table(Table $table): Table
    {
        return RoleSchema::table($table);
    }
}
