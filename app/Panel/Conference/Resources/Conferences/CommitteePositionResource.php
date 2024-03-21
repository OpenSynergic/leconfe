<?php

namespace App\Panel\Conference\Resources\Conferences;

use App\Models\ParticipantPosition;
use App\Tables\Columns\IndexColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Unique;

class CommitteePositionResource extends Resource
{
    protected static bool $isDiscovered = false;

    protected static ?string $model = ParticipantPosition::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static string $positionType = 'committee';

    public static function getModelLabel(): string
    {
        return 'Committee Position';
    }

    public static function getEloquentQuery(): Builder
    {
        return static::getModel()::query()
            ->ofType(static::$positionType)
            ->orderBy('order_column');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->unique(modifyRuleUsing: function (Unique $rule) {
                        return $rule
                            ->where('type', static::$positionType)
                            ->where('conference_id', app()->getCurrentConference()->getKey());
                    }),
                Select::make('parent_id')
                    ->relationship('parent', 'name', fn ($query, ?ParticipantPosition $record) => $query
                        ->when($record, fn ($query) => $query->whereNot('id', $record->getKey()))
                        ->ofType(static::$positionType)),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('order_column')
            ->columns([
                IndexColumn::make('no'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('parent.name')
                    ->badge(),
                Tables\Columns\TextColumn::make('participants_count')
                    ->label('Committees')
                    ->counts('participants')
                    ->badge()
                    ->color(fn (int $state) => $state > 0 ? 'primary' : 'gray'),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->using(function (ParticipantPosition $record, Tables\Actions\DeleteAction $action) {
                        try {
                            $speakerCount = $record->participants()->count();
                            if ($speakerCount > 0) {
                                throw new \Exception('Cannot delete '.$record->name.', there are '.static::$positionType.' who are still associated with this position');
                            }

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
                // Tables\Actions\CreateAction::make(),
            ])
            ->heading('Committee Positions Table')
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['type'] = static::$positionType;

                        return $data;
                    })
                    ->label('New Committee Position')
                    ->modalHeading('New Committee` Position'),
            ]);
    }

    public static function getPages(): array
    {
        return [];
    }
}
