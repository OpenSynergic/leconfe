<?php

namespace App\Panel\Livewire\Tables;

use App\Panel\Livewire\Traits\PlaceholderTrait;
use App\Schemas\UserSchema;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;

class UsersTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable, PlaceholderTrait;

    public function render()
    {
        return view('panel.livewire.tables.table');
    }

    public function table(Table $table): Table
    {
        return UserSchema::table($table);
    }
}
