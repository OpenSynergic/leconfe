<?php

namespace App\Panel\Resources\Conferences;

use Filament\Forms;
use Filament\Tables;
use App\Models\Timeline;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Panel\Resources\Conferences\TimelineResource\Pages;
use App\Panel\Resources\Conferences\TimelineResource\RelationManagers;
use App\Schemas\TimelineSchema;
use App\Schemas\TopicSchema;

class TimelineResource extends Resource
{
    protected static ?string $model = Timeline::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationGroup = 'Conferences';


    public static function form(Form $form): Form
    {
        return TimelineSchema::form($form);
    }

    public static function table(Table $table): Table
    {
        return TimelineSchema::table($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTimeline::route('/'),
        ];
    }
}