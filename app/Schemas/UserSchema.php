<?php

namespace App\Schemas;

use App\Actions\User\UserCreateAction;
use App\Actions\User\UserUpdateAction;
use App\Models\User;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Squire\Models\Country;

class UserSchema
{
  public static function table(Table $table): Table
  {
    return $table
      ->query(User::query())
      ->heading('Users')
      ->columns([
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
          EditAction::make()
            ->modalWidth('2xl')
            ->form(fn () => static::formSchemas())
            ->mutateRecordDataUsing(fn ($data, Model $record) => array_merge($data, ['meta' => $record->getAllMeta()->toArray()]))
            ->using(fn (array $data, Model $record) => UserUpdateAction::run($record, $data)),
          DeleteAction::make()
        ])
          ->button()
          ->label('Actions'),
      ])
      ->queryStringIdentifier('users')
      ->bulkActions([
        DeleteBulkAction::make()
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
        ])
    ];
  }
}
