<?php

namespace App\Schemas;

use App\Models\User;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\CommitteeMember;
use Filament\Forms\Components\Grid;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Actions\Committee\CommitteMemberInsertAction;
use Filament\Tables\Actions\ActionGroup as ActionsActionGroup;

class CommitteeMemberSchema
{
    public static function table(Table $table): Table
    {
        return $table
            ->query(CommitteeMember::query()->orderBy('order_column'))
            ->heading('Committee Members')
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('committee.position')
                    ->label('Position'),
            ])
            ->actions([
                ActionsActionGroup::make([
                    EditAction::make()
                        ->modalWidth('2xl')
                        ->form(fn () => static::formSchemas()),
                    DeleteAction::make()
                ]),
            ])
            ->queryStringIdentifier('committee_members')
            ->headerActions([
                CreateAction::make()
                    ->modalWidth('2xl')
                    ->form(fn () => static::formSchemas())
                    ->using(fn (array $data) => CommitteMemberInsertAction::run($data))
            ])
            ->filters([])
            ->reorderable('order_column')
            ->bulkActions([
                DeleteBulkAction::make()
            ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema(static::formSchemas());
    }

    public static function formSchemas(): array
    {
        return [
            Grid::make()
                ->columns(1)
                ->schema([
                    TextInput::make('name')
                        ->autocomplete(false)
                        ->datalist(fn (User $user) => $user->pluck('given_name')->toArray()),
                    Select::make('committee_id')
                        ->relationship(
                            name: 'committee',
                            titleAttribute: 'position'
                        )
                        ->label('Position'),

                ]),
        ];
    }
}
