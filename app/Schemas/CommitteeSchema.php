<?php

namespace App\Schemas;

use App\Actions\Committee\CommitteInsertAction;
use Filament\Forms\Form;
use App\Models\Committee;
use Filament\Tables\Table;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup as ActionsActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\DeleteBulkAction;
use Spatie\Tags\Tag;

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
                    DeleteAction::make()
                ])
            ])
            ->queryStringIdentifier('committees')
            ->headerActions([
                CreateAction::make()
                    ->modalWidth('2xl')
                    ->form(fn () => static::formSchemas())
                    ->using(fn ($data) => CommitteInsertAction::run($data))

            ])
            ->filters([])
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
            TextInput::make('position'),
            Textarea::make('description'),
        ];
    }
}
