<?php

namespace App\Http\Livewire\Tables;

use App\Models\Role;
use Filament\Forms\Components\TextInput;
use Livewire\Component;
use Filament\Tables;
use Filament\Tables\Actions\CreateAction;
use Illuminate\Database\Eloquent\Builder;

class CurrentRoles extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    public function render()
    {
        return view('livewire.tables.current-roles');
    }

    protected function getTableQuery(): Builder
    {
        return Role::query();
    }

    protected function getTableHeading(): string
    {
        return 'Current Roles';
    }

    protected function getTableHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->modalWidth('2xl')
                ->form([
                    TextInput::make('given_name')
                        ->required(),
                ])
        ];
    }

    protected function getTableQueryStringIdentifier(): ?string
    {
        return 'roles';
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name')
                ->size('sm'),
        ];
    }
}
