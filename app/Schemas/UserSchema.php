<?php

namespace App\Schemas;

use App\Actions\User\UserCreateAction;
use App\Actions\User\UserDeleteAction;
use App\Actions\User\UserUpdateAction;
use App\Models\Role;
use App\Models\User;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Squire\Models\Country;
use STS\FilamentImpersonate\Tables\Actions\Impersonate;

class UserSchema
{
    public static function table(Table $table): Table
    {
        return $table
            ->striped()
            ->query(User::query())
            ->heading('Users')
            ->columns([
                TextColumn::make('no')
                    ->grow(false)
                    ->state(
                        static function (HasTable $livewire, \stdClass $rowLoop): string {
                            return (string) ($rowLoop->iteration +
                                ($livewire->getTableRecordsPerPage() * ($livewire->getTablePage() - 1
                                ))
                            );
                        }
                    ),
                TextColumn::make('given_name')
                    ->size('sm')
                    ->searchable(),
                TextColumn::make('family_name')
                    ->size('sm')
                    ->searchable(),
                TextColumn::make('email')
                    ->size('sm')
                    ->searchable(),
            ])
            ->filters([
                // ...
            ])
            ->headerActions([
                CreateAction::make()
                    ->modalWidth('2xl')
                    ->outlined()
                    ->form(fn () => static::formSchemas())
                    ->using(fn (array $data) => UserCreateAction::run($data)),
            ])
            ->actions([
                ActionGroup::make([
                    Impersonate::make()
                        ->grouped()
                        ->visible(fn($record) => auth()->user()->can('loginAs', $record))
                        ->label(fn (Model $record) => "Login as {$record->given_name}")
                        ->icon('heroicon-m-key')
                        ->color('primary')
                        ->redirectTo('panel'),
                    EditAction::make()
                        ->modalWidth('2xl')
                        ->form(fn () => static::formSchemas())
                        ->mutateRecordDataUsing(fn ($data, Model $record) => array_merge($data, ['meta' => $record->getAllMeta()->toArray()]))
                        ->using(fn (array $data, Model $record) => UserUpdateAction::run($data, $record)),
                    DeleteAction::make()
                        ->using(fn (?array $data, Model $record) => UserDeleteAction::run($data, $record)),
                ]),
            ])
            ->queryStringIdentifier('users')
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema(static::formSchemas());
    }

    public static function formSchemas(): array
    {
        return [
            Grid::make()->schema([
                SpatieMediaLibraryFileUpload::make('user_profile')
                    ->collection('users_profiles')
                    ->avatar()
                    ->alignCenter()
                    ->label(''),
            ])->columns(1),
            Grid::make()
                ->schema([
                    TextInput::make('given_name')
                        ->required(),
                    TextInput::make('family_name'),
                ]),
            TextInput::make('email')
                ->disabled(fn (?Model $record) => $record)
                ->dehydrated(fn (?Model $record) => ! $record)
                ->unique(ignoreRecord: true),
            Grid::make()
                ->schema([
                    TextInput::make('password')
                        ->required(fn (?Model $record) => ! $record)
                        ->password()
                        ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                        ->dehydrated(fn ($state) => filled($state))
                        ->confirmed(),
                    TextInput::make('password_confirmation')
                        ->requiredWith('password')
                        ->password()
                        ->dehydrated(false),
                ]),
            Select::make('country')
                ->searchable()
                ->options(Country::all()->pluck('name', 'id')),
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
                ]),
        ];
    }
}
