<?php

namespace App\Panel\Resources;

use Filament\Forms;
use App\Models\Role;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Checkbox;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\PermissionRegistrar;
use App\Panel\Resources\RoleResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Panel\Resources\RoleResource\RelationManagers;
use Filament\Tables\Contracts\HasTable;

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
                            ->relationship('parent', 'name', fn ($query) => $query->whereNull('parent_id'))
                            ->searchable()
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
                    ])
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no')
                    ->grow(false)
                    ->extraCellAttributes([
                        'style' => 'width: 1px',
                    ])
                    ->state(
                        static function (HasTable $livewire, \stdClass $rowLoop): string {
                            return (string) ($rowLoop->iteration +
                                ($livewire->getTableRecordsPerPage() * ($livewire->getTablePage() - 1
                                ))
                            );
                        }
                    ),
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
            return Section::make($key)
                ->columnSpan([
                    'lg' => 2,
                    'xl' => 1,
                ])
                ->schema(
                    $permissions->map(function ($permission) {
                        [, $action] = explode(':', $permission->name);

                        return Checkbox::make('permissions.' . $permission->name)->label(Str::headline($action));
                    })
                        ->toArray()
                );
        })->toArray();

        return $permissionsGroupedByEntity;
    }
}
