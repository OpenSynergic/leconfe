<?php

namespace App\Schemas;

use App\Models\Speaker;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TagsColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use App\Actions\Conferences\CreateSpeakerAction;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

class SpeakerSchema
{

    public static function table(Table $table): Table
    {
        return $table
            ->query(Speaker::query())
            ->heading('Speaker')
            ->columns([
                SpatieMediaLibraryImageColumn::make('image')
                    ->label('Photo')
                    ->width(80)
                    ->height(80),
                TextColumn::make('name'),
                TextColumn::make('affiliation'),
                TextColumn::make('expertise'),


            ])

            ->filters([])

            ->headerActions([
                CreateAction::make()
                    ->modalWidth('2xl')
                    ->form(static::formSchemas())
                    ->using(fn (array $data) => CreateSpeakerAction::run($data))
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

    public static function form(Form $form): Form
    {
        return $form
            ->schema(static::formSchemas());
    }

    public static function formSchemas(): array
    {
        return [
            Grid::make(1)
                ->schema([
                    TextInput::make('name')
                        ->required(),
                    TagsInput::make('expertise')
                        ->required()
                        ->placeholder(''),
                    TextInput::make('affiliation')
                        ->required(),
                    SpatieMediaLibraryFileUpload::make('image')
                        ->responsiveImages()
                        ->image(),
                    Textarea::make('description'),
                ])

        ];
    }
}
