<?php

namespace App\Panel\Series\Resources;

use App\Models\Venue;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Support\Enums\FontWeight;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Infolists\Components\TextEntry;
use App\Panel\Series\Resources\VenueResource\Pages;
use Filament\Infolists\Components\TextEntry\TextEntrySize;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class VenueResource extends Resource
{
    protected static ?string $model = Venue::class;

    protected static ?string $navigationIcon = 'heroicon-o-home-modern';

    protected static ?string $navigationGroup = 'Conference';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(1)
                    ->schema([
                        TextInput::make('name')
                            ->required(),
                        TextInput::make('location')
                            ->required(),
                        SpatieMediaLibraryFileUpload::make('photo')
                            ->collection('thumbnail')
                            ->conversion('thumb')
                            ->multiple(false)
                            ->required(),
                        Textarea::make('description'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('location'),
                SpatieMediaLibraryImageColumn::make('photo')
                    ->collection('thumbnail')
                    ->conversion('thumb'),
            ])
            ->actions([
                ViewAction::make()
                    ->infolist([
                        SpatieMediaLibraryImageEntry::make('photo')
                            ->collection('thumbnail')
                            ->conversion('thumb')
                            ->label('')
                            ->visible(fn ($record) => $record->hasMedia('thumbnail')),
                        TextEntry::make('name')
                            ->size(TextEntrySize::Large)
                            ->weight(FontWeight::Bold)
                            ->label('')
                            ->color('secondary'),
                        TextEntry::make('location')
                            ->label('Location')
                            ->color('secondary')
                            ->icon('heroicon-m-map-pin'),
                        TextEntry::make('description')
                            ->color('secondary'),
                    ]),
                ActionGroup::make([
                    EditAction::make()
                        ->modalWidth('2xl')
                        ->form(fn ($form) => static::form($form)),
                    DeleteAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageVenues::route('/'),
        ];
    }
}
