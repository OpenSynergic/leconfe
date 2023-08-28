<?php

namespace App\Panel\Resources\Conferences;

use App\Models\UserContent;
use App\Panel\Resources\Conferences\AnnouncementResource\Pages;
use App\Schemas\AnnouncementSchema;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class AnnouncementResource extends Resource
{
    protected static ?string $model = UserContent::class;

    protected static ?string $modelLabel = 'Announcement';

    protected static ?string $navigationGroup = 'Conferences';

    protected static ?string $navigationIcon = 'heroicon-o-speaker-wave';

    public static function form(Form $form): Form
    {
        return AnnouncementSchema::form($form);
    }

    public static function table(Table $table): Table
    {
        return AnnouncementSchema::table($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAnnouncements::route('/'),
        ];
    }
}
