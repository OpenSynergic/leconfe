<?php

namespace App\Panel\Resources\Conferences;

use App\Models\Venue;
use App\Panel\Resources\Concern\HasNavigationBadge;
use App\Panel\Resources\Conferences\VenueResource\Pages;
use App\Schemas\VenueSchema;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class VenueResource extends Resource
{
    use HasNavigationBadge;

    protected static ?string $navigationGroup = 'Conferences';

    protected static ?string $model = Venue::class;

    protected static ?string $navigationIcon = 'heroicon-o-home-modern';

    public static function form(Form $form): Form
    {
        return VenueSchema::form($form);
    }

    public static function table(Table $table): Table
    {
        return VenueSchema::table($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageVenues::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
