<?php

namespace App\Panel\Resources\Conferences;

use App\Panel\Resources\Conferences\AnnouncementResource\Pages;
use App\Models\Announcement;
use App\Schemas\AnnouncementSchema;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AnnouncementResource extends Resource
{
    protected static ?string $model = Announcement::class;

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
