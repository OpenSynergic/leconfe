<?php

namespace App\Panel\Resources;

use App\Panel\Resources\RoleResource\Pages;
use App\Panel\Resources\RoleResource\RelationManagers;
use App\Models\Role;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Spatie\Permission\PermissionRegistrar;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 6;


    public static function getEloquentQuery(): Builder
    {
        return static::getModel()::query()->with('parent');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        TextInput::make('name'),
                        Select::make('parent_id')
                            ->label('Permission Level')
                            ->required()
                            ->relationship('parent', 'name', fn($query) => $query->whereNull('parent_id'))
                            ->searchable()
                            ->preload(),
                    ])
                    ->columns([
                        'sm' => 2,
                    ]),
                Section::make('Advanced Permissions')
                    ->schema(static::getPermissionEntitySchema())
                    ->columns([
                        'sm' => 4,
                    ])
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('parent.name')
                    ->label('Permission Level')
                    ->badge(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }

    public static function getPermissionEntitySchema() : array
    {
        $permissions = app(PermissionRegistrar::class)->getPermissions();
        
        $permissionsGroupedByEntity = $permissions->groupBy(function($permission){
            [$context, $action] = explode(':', $permission->name);

            return $context;
        })->map(function($permissions, $key){
            return Section::make($key)
                ->schema($permissions->map(fn($permission) => Checkbox::make($permission->id)->label($permission->name))
                ->toArray()
            );
        })->toArray();

        


        return $permissionsGroupedByEntity;
    }
}
