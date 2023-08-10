<?php

namespace App\Schemas;

use App\Models\Venue;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Grid;
use Filament\Support\Enums\FontWeight;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use App\Actions\Conferences\CreateVenueAction;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Illuminate\Database\Eloquent\Model;

class VenueSchema
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('location'),
                SpatieMediaLibraryImageColumn::make('photo')
                    ->collection('venue_photos')
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
                    SpatieMediaLibraryFileUpload::make('photo')
                        ->collection('venue_photos')
                        ->required(),
                    Textarea::make('description')
                ])
        ];
    }

    public static function infoListSchemas(): array
    {
        return [
            Section::make('Venue')
                ->schema([
                    SpatieMediaLibraryImageEntry::make('photo')
                        ->collection('venue_photos')
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
