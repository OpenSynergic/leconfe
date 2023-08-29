<?php

namespace App\Panel\Resources\Conferences;

use App\Models\Speaker;
use App\Panel\Resources\Conferences\SpeakerResource\Pages;
use App\Schemas\SpeakerSchema;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;

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
}
