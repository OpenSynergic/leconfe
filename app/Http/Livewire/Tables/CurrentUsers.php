<?php

namespace App\Http\Livewire\Tables;

use App\Actions\User\CreateUser;
use App\Actions\User\UpdateUser;
use App\Models\User;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Livewire\Component;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Squire\Models\Country;

class CurrentUsers extends Component implements HasTable, HasForms
{
    use InteractsWithTable, InteractsWithForms;

    public function render()
    {
        return view('livewire.tables.current-users');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(User::query())
            ->heading('Current Users')
            ->columns([
                Tables\Columns\TextColumn::make('given_name')
                    ->size('sm')
                    ->searchable(),
                Tables\Columns\TextColumn::make('family_name')
                    ->size('sm')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->size('sm')
                    ->searchable(),
            ])
            ->filters([
                // ...
            ])
            ->headerActions([
                CreateAction::make()
                    ->modalWidth('2xl')
                    ->form($this->getUserFormSchema())
                    ->using(fn (array $data) => CreateUser::run($data)),
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make()
                        ->modalWidth('2xl')
                        ->form($this->getUserFormSchema())
                        ->mutateRecordDataUsing(fn ($data, Model $record) => array_merge($data, ['meta' => $record->getAllMeta()->toArray()]))
                        ->using(fn (array $data, Model $record) => UpdateUser::run($data, $record)),
                    DeleteAction::make()
                ]),
            ])
            ->queryStringIdentifier('users')
            ->deferLoading(true)
            ->bulkActions([
                DeleteBulkAction::make()
            ]);
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
}
