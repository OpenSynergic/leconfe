<?php

namespace App\Livewire\Tables;

use App\Livewire\Traits\PlaceholderTrait;
use App\Schemas\UserSchema;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Livewire\Component;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class UsersTable extends Component implements HasTable, HasForms
{
    use InteractsWithTable, InteractsWithForms, PlaceholderTrait;

    public function render()
    {
        return view('livewire.tables.table');
    }

    public function table(Table $table): Table
    {
        return UserSchema::table($table);
    }
}
