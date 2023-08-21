<?php

namespace App\Schemas;

use App\Actions\Committee\CommitteInsertAction;
use App\Models\Committee;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Tables\Actions\ActionGroup as ActionsActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CommitteeSchema
{
    public static function table(Table $table): Table
    {
        return $table
            ->query(Committee::query())
            ->heading('Committee Structure')
            ->columns([
                TextColumn::make('position'),
            ])
            ->actions([
                ActionsActionGroup::make([
                    EditAction::make()
                        ->modalWidth('2xl')
                        ->form(fn () => static::formSchemas()),
                    DeleteAction::make(),
                ]),
            ])
            ->queryStringIdentifier('committees')
            ->headerActions([
                CreateAction::make()
                    ->modalWidth('2xl')
                    ->form(fn () => static::formSchemas())
                    ->using(fn ($data) => CommitteInsertAction::run($data)),

            ])
            ->filters([])
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
            TextInput::make('position'),
            Textarea::make('description'),
        ];
    }
}
