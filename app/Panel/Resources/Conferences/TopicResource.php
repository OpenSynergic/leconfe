<?php

namespace App\Panel\Resources\Conferences;

use App\Models\Topic;
use App\Panel\Resources\Concern\HasNavigationBadge;
use App\Panel\Resources\Conferences\TopicResource\Pages;
use App\Schemas\TopicSchema;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class TopicResource extends Resource
{
    use HasNavigationBadge;

    protected static ?string $navigationGroup = 'Conferences';

    protected static ?string $model = Topic::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left';

    public static function form(Form $form): Form
    {
        return TopicSchema::form($form);
    }

    public static function table(Table $table): Table
    {
        return TopicSchema::table($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTopics::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
