<?php

namespace App\Panel\ScheduledConference\Widgets;

use App\Models\Enums\SubmissionStatus;
use App\Models\Submission;
use Filament\Actions\Action;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class SubmissionDetailsTableWidget extends BaseWidget
{
    protected static bool $isDiscovered = false;

    protected static ?string $heading = 'Paper Details';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Submission::query()
                    ->with(['authors', 'meta'])
                    ->select('submissions.id', 'submissions.scheduled_conference_id', 'submissions.proceeding_id')
                    ->selectRaw('COALESCE(SUM(CASE WHEN analytic_metrics.assoc_type != 4 THEN analytic_metrics.metric ELSE 0 END), 0) as abstract_views')
                    ->selectRaw('COALESCE(SUM(CASE WHEN analytic_metrics.assoc_type = 4 THEN analytic_metrics.metric ELSE 0 END), 0) as file_views')
                    ->selectRaw('COALESCE(SUM(analytic_metrics.metric), 0) as total_views')
                    ->leftJoin('analytic_metrics', 'submissions.id', '=', 'analytic_metrics.submission_id')
                    ->groupBy('submissions.id', 'submissions.scheduled_conference_id', 'submissions.proceeding_id')
                    ->orderByDesc('total_views')
            )
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable()
                    ->grow(false),

                Tables\Columns\TextColumn::make('title')
                    ->label(__('general.title'))
                    ->getStateUsing(fn (Submission $record) => $record->getMeta('title') ?? "Paper #{$record->id}")
                    ->description(function (Submission $record) {
                        $authors = $record->authors->map(fn ($author) => trim("{$author->first_name} {$author->last_name}"))->filter()->join(', ');
                        return $authors ?: null;
                    })
                    ->searchable(query: function (Builder $query, string $search) {
                        $query->where('submissions.id', $search)
                            ->orWhereHas('meta', function ($q) use ($search) {
                                $q->where('key', 'title')->where('value', 'like', "%{$search}%");
                            })
                            ->orWhereHas('authors', function ($q) use ($search) {
                                $q->where('first_name', 'like', "%{$search}%")
                                  ->orWhere('last_name', 'like', "%{$search}%");
                            });
                    })
                    ->wrap(),

                Tables\Columns\TextColumn::make('abstract_views')
                    ->label('Abstract Views')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('file_views')
                    ->label('File Downloads')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_views')
                    ->label('Total')
                    ->numeric()
                    ->badge()
                    ->color('primary')
                    ->sortable(),
            ])
            ->actions([
                Action::make('view')
                    ->label(__('general.view'))
                    ->icon('heroicon-o-eye')
                    ->url(fn (Submission $record) => route('livewirePageGroup.conference.pages.paper', ['submission' => $record->id]), true),
            ])
            ->searchPlaceholder('Search by title, author and ID')
            ->emptyStateHeading('No papers')
            ->defaultPaginationPageOption(15);
    }
}
