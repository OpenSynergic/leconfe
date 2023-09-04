<?php

namespace App\Panel\Resources;

use App\Models\Role;
use App\Panel\Resources\RoleResource\Pages;
use App\Tables\Columns\IndexColumn;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 6;

    protected static ?Role $parentRole = null;

    public static function getEloquentQuery(): Builder
    {
        return static::getModel()::query();
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
                            ->relationship('parent', 'name', fn ($query) => $query->whereNull('parent_id'))
                            ->live()
                            ->afterStateUpdated(function (Set $set, ?string $state) {
                                if (! $state) {
                                    return;
                                }

                                $newParent = Role::find($state);

                                app(PermissionRegistrar::class)
                                    ->getPermissions()
                                    ->each(function (Permission $permission) use ($set, $newParent) {
                                        $condition = $newParent->hasPermissionOnAncestorsAndSelf($permission);

                                        $set('permissions.'.$permission->name, $condition);
                                    });
                            })
                            ->preload(),
                    ])
                    ->columns([
                        'sm' => 2,
                    ]),
                Section::make('Advanced Permissions')
                    ->schema(static::getPermissionEntitySchema())
                    ->collapsible(false)
                    ->columns([
                        'sm' => 4,
                    ]),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                IndexColumn::make('no'),
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
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
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
            'view' => Pages\ViewRole::route('/{record}'),
        ];
    }

    public static function getPermissionEntitySchema(): array
    {
        $permissions = app(PermissionRegistrar::class)->getPermissions();

        $permissionsGroupedByEntity = $permissions->groupBy(function ($permission) {
            // Split the permission name by the first colon
            // Example : "User:update" become ["User", "update"]
            [$context, $action] = explode(':', $permission->name);

            return $context;
        })->map(function ($permissions, $key) {
            return Fieldset::make($key)
                ->columns(1)
                ->extraAttributes([
                    'class' => 'h-full',
                ])
                ->disabled(fn ($record) => ! auth()->user()->can('assignPermissions', $record))
                ->columnSpan([
                    'lg' => 2,
                    'xl' => 1,
                ])
                ->schema($permissions->map(function ($permission) {
                    [, $action] = explode(':', $permission->name);

                    return Checkbox::make('permissions.'.$permission->name)
                        ->disabled(function (Get $get) use ($permission) {
                            $parentId = $get('parent_id');

                            if (! $parentId) {
                                return false;
                            }

                            $parent = static::getParentRole($parentId);

                            return $parent->hasPermissionOnAncestorsAndSelf($permission);
                        })
                        ->label(Str::headline($action));
                })->toArray());
        })->toArray();

        return $permissionsGroupedByEntity;
    }

    public static function getParentRole(string $parentId)
    {
        if (static::$parentRole?->getKey() != $parentId) {
            static::$parentRole = Role::find($parentId);
        }

        return static::$parentRole;
    }
}
