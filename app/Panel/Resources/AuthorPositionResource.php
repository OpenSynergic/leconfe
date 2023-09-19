<?php

namespace App\Panel\Resources;

use App\Models\ParticipantPosition;
use App\Tables\Columns\IndexColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Unique;

class AuthorPositionResource extends Resource
{
    protected static bool $isDiscovered = false;

    protected static ?string $model = ParticipantPosition::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static string $positionType = 'author';

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
                Tables\Columns\TextColumn::make('participants_count')
                    ->label('Authors')
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
                            $authorCount = $record->participants()->count();
                            if ($authorCount > 0) {
                                throw new \Exception('Cannot delete ' . $record->name . ', there are authors who are still associated with this position');
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
            ->heading('Author Positions Table')
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['type'] = static::$positionType;

                        return $data;
                    })
                    ->label('New Author Position')
                    ->modalHeading('New Author Position'),
            ]);
    }

    public static function getPages(): array
    {
        return [];
    }
}
