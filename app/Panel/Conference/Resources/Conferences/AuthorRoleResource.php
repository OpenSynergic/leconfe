<?php

namespace App\Panel\Conference\Resources\Conferences;

use App\Models\AuthorRole;
use App\Tables\Columns\IndexColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Unique;

class AuthorRoleResource extends Resource
{
    protected static bool $isDiscovered = false;

    protected static ?string $model = AuthorRole::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static string $roleType = 'author';

    public static function getEloquentQuery(): Builder
    {
        return static::getModel()::query()
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
                Tables\Columns\TextColumn::make('authors_count')
                    ->label('Authors')
                    ->counts('authors')
                    ->badge()
                    ->color(fn (int $state) => $state > 0 ? 'primary' : 'gray'),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->using(function (AuthorRole $record, Tables\Actions\DeleteAction $action) {
                        try {
                            $authorCount = $record->authors()->count();
                            if ($authorCount > 0) {
                                throw new \Exception('Cannot delete '.$record->name.', there are authors who are still associated with this role');
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
            ->heading('Author Roles Table')
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        return $data;
                    })
                    ->label('New Author Role')
                    ->modalHeading('New Author Role'),
            ]);
    }

    public static function getPages(): array
    {
        return [];
    }
}
