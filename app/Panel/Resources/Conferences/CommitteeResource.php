<?php

namespace App\Panel\Resources\Conferences;

use App\Models\CommitteeMember;
use App\Panel\Resources\Conferences\CommitteeResource\Pages;
use App\Schemas\CommitteeMemberSchema;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;

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
