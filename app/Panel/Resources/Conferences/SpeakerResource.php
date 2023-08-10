<?php

namespace App\Panel\Resources\Conferences;

use App\Panel\Resources\Conferences\SpeakerResource\Pages;
use App\Panel\Resources\Conferences\SpeakerResource\RelationManagers;
use App\Models\Speaker;
use App\Schemas\SpeakerSchema;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SpeakerResource extends Resource
{
    protected static ?string $navigationGroup = 'Conferences';

    protected static ?string $model = Speaker::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        return SpeakerSchema::form($form);
    }

    public static function table(Table $table): Table
    {
        return SpeakerSchema::table($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageSpeakers::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
