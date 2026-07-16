<?php

namespace App\Panel\ScheduledConference\Resources;

use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Exception;
use Throwable;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\CreateAction;
use App\Models\CommitteeRole;
use App\Tables\Columns\IndexColumn;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Unique;

class CommitteeRoleResource extends Resource
{
    protected static bool $isDiscovered = false;

    protected static ?string $model = CommitteeRole::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static string $roleType = 'committee';

    public static function getModelLabel(): string
    {
        return 'Committee Role';
    }

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
                            ->where('scheduled_conference_id', app()->getCurrentScheduledConference()->getKey());
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
                TextColumn::make('committees_count')
                    ->label(__('general.committees'))
                    ->counts('committees')
                    ->badge()
                    ->color(fn (int $state) => $state > 0 ? 'primary' : 'gray'),

            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->using(function (CommitteeRole $record, DeleteAction $action) {
                        try {
                            $speakerCount = $record->committees()->count();
                            if ($speakerCount > 0) {
                                throw new Exception(__('general.cannot_delete_role_commites', ['name' => $record->name, 'roleType' => static::$roleType]));
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
            ->emptyStateActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->heading(__('general.committee_roles_table'))
            ->headerActions([
                CreateAction::make()
                    ->mutateDataUsing(function (array $data): array {
                        return $data;
                    })
                    ->label(__('general.new_committee_role'))
                    ->modalHeading(__('general.new_committee_role')),
            ]);
    }

    public static function getPages(): array
    {
        return [];
    }
}
