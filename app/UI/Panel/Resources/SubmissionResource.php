<?php

namespace App\UI\Panel\Resources;

use App\UI\Panel\Resources\SubmissionResource\Pages;
use App\Models\Submission;
use App\Schemas\SubmissionSchema;
use Filament\GlobalSearch\GlobalSearchResult;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class SubmissionResource extends Resource
{
    protected static ?string $model = Submission::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $recordTitleAttribute = 'title';

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->getMeta('title');
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['user']);
    }

    public static function getRecordTitle(?Model $record): string | Htmlable | null
    {
        return $record?->getMeta('title') ?? static::getModelLabel();
    }



    public static function getEloquentQuery(): Builder
    {
        return static::getModel()::with(['meta']);
    }

    public static function getGlobalSearchResults(string $search): Collection
    {
        $search = strtolower($search);

        $query = static::getGlobalSearchEloquentQuery();

        $isFirst = true;
        foreach (explode(' ', $search) as $searchWord) {
            $whereHas = $isFirst ? 'whereHas' : 'orWhereHas';

            $query->{$whereHas}('meta', function (Builder $q) use ($searchWord) {
                $q->whereIn('key', static::getGloballySearchableAttributes())
                    ->where('value', 'like', "%{$searchWord}%");
            });

            $isFirst = false;
        }

        return $query
            ->limit(static::getGlobalSearchResultsLimit())
            ->get()
            ->map(function (Model $record): ?GlobalSearchResult {
                $url = static::getGlobalSearchResultUrl($record);
                if (blank($url)) {
                    return null;
                }

                return new GlobalSearchResult(
                    title: static::getGlobalSearchResultTitle($record),
                    url: $url,
                    details: static::getGlobalSearchResultDetails($record),
                    actions: static::getGlobalSearchResultActions($record),
                );
            })
            ->filter();
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Author' => $record->user->name,
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'description'];
    }


    public static function table(Table $table): Table
    {
        return (SubmissionSchema::table($table))
            ->columns([
                TextColumn::make('user.name')
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->url(fn (Submission $record) => static::getUrl('view', [
                        'record' => $record->id,
                    ])),
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

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
