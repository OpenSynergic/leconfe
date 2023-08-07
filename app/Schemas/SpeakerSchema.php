<?php

namespace App\Schemas;

use App\Models\Speaker;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Grid;
use App\Actions\Conference\CreateSpeakerAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TagsColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;

class SpeakerSchema
{

    public static function table(Table $table) : Table
    {
        return $table
        ->query(Speaker::query())
        ->heading('Speaker')
        ->columns([
            TextColumn::make('name'),
            TagsColumn::make('expertise'),
            ])

        ->filters([

        ])

        ->headerActions([
            CreateAction::make()
            ->modalWidth('2xl')
            ->form(static::formSchemas())
            ->using(fn(array $data) => CreateSpeakerAction::run($data))
        ])

        ->actions([
            ViewAction::make()
            ->form(static::formSchemas()),
            ActionGroup::make([
                EditAction::make()
                ->modalWidth('2xl')
                ->form(static::formSchemas()),
                DeleteAction::make()

            ])
            ]);



    }

    public static function form(Form $form) : Form
    {
        return $form
        ->schema(static::formSchemas());
    }

    public static function formSchemas() : array
    {
        return [
            Grid::make()
            ->schema([
                TextInput::make('name')
                ->required(),
               TagsInput::make('expertise')
               ->required()
               ->placeholder(''),
            ])

        ];
    }

}
