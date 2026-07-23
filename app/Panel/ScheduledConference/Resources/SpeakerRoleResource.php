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
use App\Models\SpeakerRole;
use App\Tables\Columns\IndexColumn;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Unique;

class SpeakerRoleResource extends Resource
{
    protected static bool $isDiscovered = false;

    protected static ?string $model = SpeakerRole::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static string $roleType = 'speaker';

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
                TextColumn::make('speakers_count')
                    ->label(__('general.speakers'))
                    ->counts('speakers')
                    ->badge()
                    ->color(fn (int $state) => $state > 0 ? 'primary' : 'gray'),

            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->using(function (SpeakerRole $record, DeleteAction $action) {
                        try {
                            $speakerCount = $record->speakers()->count();
                            if ($speakerCount > 0) {
                                throw new Exception(__('general.cannot_delete_speakers_role', ['variable' => $record->name]));
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
            ->heading(__('general.speaker_roles_table'))
            ->headerActions([
                CreateAction::make()
                    ->mutateDataUsing(function (array $data): array {
                        return $data;
                    })
                    ->label(__('general.new_speaker_role'))
                    ->modalHeading(__('general.new_speaker_role')),
            ]);
    }

    public static function getPages(): array
    {
        return [];
    }
}
