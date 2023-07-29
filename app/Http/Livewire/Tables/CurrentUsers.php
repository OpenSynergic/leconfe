<?php

namespace App\Http\Livewire\Tables;

use App\Actions\User\CreateUser;
use App\Actions\User\UpdateUser;
use App\Models\User;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Livewire\Component;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Squire\Models\Country;

class CurrentUsers extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    public function render()
    {
        return view('livewire.tables.current-users');
    }

    protected function getTableQuery(): Builder
    {
        return User::query();
    }

    protected function getTableHeading(): string
    {
        return 'Current Users';
    }

    protected function getTableHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->modalWidth('2xl')
                ->form($this->getUserFormSchema())
                ->using(fn (array $data) => CreateUser::run($data))
        ];
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('given_name')
                ->size('sm')
                ->searchable(),
            Tables\Columns\TextColumn::make('family_name')
                ->size('sm')
                ->searchable(),
            Tables\Columns\TextColumn::make('email')
                ->size('sm')
                ->searchable(),
        ];
    }

    protected function getUserFormSchema(): array
    {
        return [
            Grid::make()
                ->schema([
                    TextInput::make('given_name')
                        ->required(),
                    TextInput::make('family_name'),
                ]),
            TextInput::make('email')
                ->disabled(fn (?Model $record) => $record)
                ->dehydrated(fn (?Model $record) => !$record)
                ->unique(ignoreRecord: true),
            Grid::make()
                ->schema([
                    TextInput::make('password')
                        ->required(fn (?Model $record) => !$record)
                        ->password()
                        ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                        ->dehydrated(fn ($state) => filled($state))
                        ->confirmed(),
                    TextInput::make('password_confirmation')
                        ->requiredWith('password')
                        ->password()
                        ->dehydrated(false)
                ]),
            Select::make('country')
                ->searchable()
                ->options(fn () => Country::all()->pluck('name', 'id')),
            Section::make('User Details')
                ->collapsible()
                ->collapsed()
                ->schema([
                    Grid::make(2)
                        ->schema([
                            TextInput::make('meta.phone'),
                            TextInput::make('meta.orcid_id')
                                ->label('ORCID iD'),
                            TextInput::make('meta.affiliation'),
                        ]),
                ])
        ];
    }

    protected function getTableActions(): array
    {
        return [
            ActionGroup::make([
                EditAction::make()
                    ->modalWidth('2xl')
                    ->form($this->getUserFormSchema())
                    ->mutateRecordDataUsing(fn ($data, Model $record) => array_merge($data, ['meta' => $record->getAllMeta()->toArray()]))
                    ->using(fn (array $data, Model $record) => UpdateUser::run($data, $record)),
                DeleteAction::make()
            ]),
        ];
    }

    protected function getTableBulkActions(): array
    {
        return [
            DeleteBulkAction::make()
        ];
    }

    protected function getTableQueryStringIdentifier(): ?string
    {
        return 'users';
    }

    public function isTableLoadingDeferred(): bool
    {
        return true;
    }
}
