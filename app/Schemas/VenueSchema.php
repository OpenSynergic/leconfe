<?php

namespace App\Schemas;

use App\Models\Venue;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Grid;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use App\Actions\Conferences\CreateVenueAction;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;

class VenueSchema
{
    public static function table(Table $table): Table
    {
        return $table
            ->query(Venue::query())
            ->heading('Venue')
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('location')
                    ->wrap(),
                SpatieMediaLibraryImageColumn::make('image')
                    ->width(80)
                    ->height(80)
                    ->label(''),
            ])

            ->headerActions([
                CreateAction::make()
                    ->modalWidth('2xl')
                    ->form(static::formSchemas())
                    ->using(fn (array $data) => CreateVenueAction::run($data)),
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
                    TextInput::make('location')
                        ->required(),
                    SpatieMediaLibraryFileUpload::make('image')
                        ->responsiveImages()
                        ->image()
                        ->label('Venue Photo')
                ])
        ];
    }
}
