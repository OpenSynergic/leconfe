<?php

namespace App\Schemas;

use App\Models\Role;
use Filament\Forms\Form;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RoleSchema
{
    public static function table(Table $table): Table
    {
        return $table
            ->query(Role::query())
            ->heading('Roles')
            ->columns([
                TextColumn::make('name')
                    ->size('sm'),
            ])
            ->filters([
                // ...
            ])
            ->headerActions([
                CreateAction::make()
                    ->modalWidth('2xl')
                    ->form(static::formSchemas()),
            ])
            ->actions([])
            ->queryStringIdentifier('roles')
            ->bulkActions([]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema(static::formSchemas());
    }

    public static function formSchemas(): array
    {
        return [];
    }
}
