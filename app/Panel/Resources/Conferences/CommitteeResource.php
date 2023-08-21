<?php

namespace App\Panel\Resources\Conferences;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\CommitteeMember;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Forms\Components\Tabs;
use App\Schemas\CommitteeMemberSchema;
use Illuminate\Database\Eloquent\Builder;
use App\Infolists\Components\LivewireEntry;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Panel\Resources\Conferences\CommitteeResource\Pages;
use App\Panel\Resources\Conferences\CommitteeResource\RelationManagers;

class CommitteeResource extends Resource
{
    protected static ?string $navigationGroup = 'Conferences';
    protected static ?string $model = CommitteeMember::class;

    protected static ?string $modelLabel = 'Committees';

    protected static ?int $navigationSort = 4;


    protected static ?string $navigationLabel = 'Committees';

    protected static ?string $navigationIcon = 'heroicon-o-user-group';



    public static function form(Form $form): Form
    {
        return CommitteeMemberSchema::form($form);
    }

    public static function table(Table $table): Table
    {
        return CommitteeMemberSchema::table($table);
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
            'index' => Pages\ManageCommittee::route('/'),
        ];
    }
}
