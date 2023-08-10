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
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\ImageColumn;
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
            ])

            ->headerActions([
                CreateAction::make()
                    ->modalWidth('2xl')
                    ->form(static::formSchemas())
                    ->using(fn (array $data) => CreateVenueAction::run($data)),
            ])

            ->actions([
                ViewAction::make()
                    ->infolist(static::infoListSchemas()),
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
                    FileUpload::make('image')
                        ->required()
                        ->multiple(),
                    Textarea::make('description')
                ])
        ];
    }

    public static function infoListSchemas(): array
    {
        return [
            Section::make('Venue')
                ->schema([
                    ImageEntry::make('image')
                        ->width(150)
                        ->height(150)
                        ->label(''),
                    TextEntry::make('name')
                        ->weight(FontWeight::Bold)
                        ->label('')
                        ->color('secondary'),
                    TextEntry::make('location')
                        ->label('Location')
                        ->color('secondary')
                        ->icon('heroicon-m-map-pin'),
                    TextEntry::make('description')
                        ->color('secondary')
                ]),
        ];
    }
}
