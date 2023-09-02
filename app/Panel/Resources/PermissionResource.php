<?php

namespace App\Panel\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Permission;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Panel\Resources\PermissionResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Panel\Resources\PermissionResource\RelationManagers;
use Filament\Forms\Get;

class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;

    protected static ?string $navigationIcon = 'heroicon-o-lock-closed';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 7;

    /**
     * This Resource is only for development purposes.
     */
    public static function isDiscovered(): bool
    {
        return !app()->isProduction();
    }

    public static function getEloquentQuery(): Builder
    {
        return static::getModel()::query();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('context')
                    ->datalist(fn () => static::getEloquentQuery()->pluck('context')->unique()->sort()->values()->all())
                    ->dehydrateStateUsing(fn (string $state): string => Str::studly($state))
                    ->helperText('Action must be StudlyCase'),
                TextInput::make('action')
                    ->datalist(fn () => static::getEloquentQuery()->pluck('action')->unique()->sort()->values()->all())
                    ->helperText('Action must be camelCase')
                    ->dehydrateStateUsing(fn (string $state): string => Str::camel($state)),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            // ->deferLoading()
            ->columns([
                TextColumn::make('context')
                    ->sortable()
                    ->formatStateUsing(fn (string $state): string => Str::headline($state))
                    ->searchable(),
                TextColumn::make('action')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn (string $state): string => Str::headline($state)),
            ])
            ->groups([
                Group::make('context'),
                Group::make('action')
                    ->getTitleFromRecordUsing(fn (Permission $permission): string => Str::headline($permission->action)),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->using(function (Permission $record, Tables\Actions\DeleteAction $action) {
                        try {
                            return $record->delete();
                        } catch (\Throwable $th) {
                            $action->failureNotificationTitle($th->getMessage());
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManagePermissions::route('/'),
        ];
    }
}
