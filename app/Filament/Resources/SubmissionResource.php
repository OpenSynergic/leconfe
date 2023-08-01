<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubmissionResource\Pages;
use App\Filament\Resources\SubmissionResource\RelationManagers;
use App\Models\Submission;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SubmissionResource extends Resource
{
    protected static ?string $model = Submission::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function getEloquentQuery(): Builder
    {
        return static::getModel()::with('meta');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                Tables\Actions\Action::make('create')
                    ->button()
                    ->hidden(fn () => setting('disable_submission'))
                    ->url(static::getUrl('create'))
                    ->label('New Submission')
            ])
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->getStateUsing(fn (Submission $record) => $record->getMeta('title'))
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query
                            ->whereMeta('title', 'like', "%{$search}%");
                    })
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->url(fn (Submission $record) => static::getUrl('view', [
                        'record' => $record->id,
                        // 'step' => $record->submission_progress
                    ])),
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageSubmissions::route('/'),
            'create' => Pages\CreateSubmission::route('/create'),
            'complete' => Pages\CompleteSubmission::route('/complete/{record}'),
            'view' => Pages\ViewSubmission::route('/{record}'),
        ];
    }
}
