<?php

namespace App\Panel\Resources;

use App\Panel\Resources\StaticPageResource\Pages;
use App\Panel\Resources\StaticPageResource\RelationManagers;
use App\Models\StaticPage;
use App\Schemas\StaticPageSchema;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StaticPageResource extends Resource
{
    protected static ?string $model = StaticPage::class;

    protected static ?string $navigationGroup = 'Conferences';

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    public static function form(Form $form): Form
    {
        return StaticPageSchema::form($form);
    }

    public static function table(Table $table): Table
    {
        return StaticPageSchema::table($table)->defaultSort('created_at', 'desc');;
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
            'index' => Pages\ListStaticPages::route('/'),
            'create' => Pages\CreateStaticPage::route('/create'),
            'edit' => Pages\EditStaticPage::route('/{record}/edit'),
        ];
    }    
}
