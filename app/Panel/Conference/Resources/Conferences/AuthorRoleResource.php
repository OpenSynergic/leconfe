<?php

namespace App\Panel\Conference\Resources\Conferences;

use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Exception;
use Throwable;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\CreateAction;
use App\Panel\Conference\Resources\Conferences\AuthorRoleResource\Pages\ManageAuthorRoles;
use App\Models\AuthorRole;
use App\Panel\Conference\Resources\Conferences\AuthorRoleResource\Pages;
use App\Tables\Columns\IndexColumn;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Unique;

class AuthorRoleResource extends Resource
{
    protected static bool $isDiscovered = false;

    protected static ?string $model = AuthorRole::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Conferences';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-users';

    public static function getNavigationLabel(): string
    {
        return 'Author';
    }

    public static string $roleType = 'author';

    public static function getEloquentQuery(): Builder
    {
        return static::getModel()::query()
            ->orderBy('order_column');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('general.name'))
                    ->required()
                    ->unique(modifyRuleUsing: function (Unique $rule) {
                        return $rule
                            ->where('conference_id', app()->getCurrentConference()->getKey());
                    }, ignoreRecord: true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('order_column')
            ->columns([
                IndexColumn::make('no'),
                TextColumn::make('name')
                    ->label(__('general.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('authors_count')
                    ->label(__('general.authors'))
                    ->counts('authors')
                    ->badge()
                    ->color(fn (int $state) => $state > 0 ? 'primary' : 'gray'),

            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->using(function (AuthorRole $record, DeleteAction $action) {
                        try {
                            $authorCount = $record->authors()->count();
                            if ($authorCount > 0) {
                                throw new Exception(__('general.cannot_delete_role', ['variable' => $record->name]));
                            }

                            return $record->delete();
                        } catch (Throwable $th) {
                            $action->failureNotificationTitle($th->getMessage());
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                CreateAction::make()
                    ->mutateDataUsing(function (array $data): array {
                        return $data;
                    })
                    ->label(__('general.new_author_role'))
                    ->modalHeading(__('general.new_author_role')),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageAuthorRoles::route('/'),
        ];
    }
}
