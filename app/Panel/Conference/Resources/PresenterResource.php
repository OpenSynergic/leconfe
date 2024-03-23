<?php

namespace App\Panel\Conference\Resources;

use App\Panel\Conference\Resources\PresenterResource\Pages;
use App\Panel\Conference\Resources\PresenterResource\RelationManagers;
use App\Models\Presenter;
use App\Tables\Columns\IndexColumn;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PresenterResource extends BaseResource
{
    protected static ?string $model = Presenter::class;

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function getEloquentQuery(): Builder
    {
        return static::getModel()::query()
            ->with(['media', 'meta']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultGroup('submission.id')
            ->groups([
                Group::make('submission.id')
                    ->label('Group by Submission')
                    ->titlePrefixedWithLabel(false)
                    ->getTitleFromRecordUsing(fn (Presenter $record): string => 'Submission : '.ucfirst($record->submission->getMeta('title')))
                    ->collapsible(),
            ])
            ->columns([
                IndexColumn::make('no')
                ->toggleable(),
                SpatieMediaLibraryImageColumn::make('profile')
                    ->collection('profile')
                    ->conversion('avatar')
                    ->width(50)
                    ->height(50)
                    ->extraCellAttributes([
                        'style' => 'width: 1px',
                    ])
                    ->circular()
                    ->defaultImageUrl(fn (Model $record): string => $record->getFilamentAvatarUrl())
                    ->toggleable(),
                TextColumn::make('full_name')
                    ->searchable(
                        query: fn ($query, $search) => $query
                            ->where('given_name', 'LIKE', "%{$search}%")
                            ->orWhere('family_name', 'LIKE', "%{$search}%")
                    )
                    ->toggleable(),
                TextColumn::make('email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListPresenters::route('/'),
            'create' => Pages\CreatePresenter::route('/create'),
            'edit' => Pages\EditPresenter::route('/{record}/edit'),
        ];
    }
}
