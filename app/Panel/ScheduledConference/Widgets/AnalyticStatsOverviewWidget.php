<?php

namespace App\Panel\ScheduledConference\Widgets;

use App\Models\AnalyticMetric;
use App\Models\Submission;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AnalyticStatsOverviewWidget extends BaseWidget
{
    protected static bool $isDiscovered = false;

    protected int|string|array $columnSpan = 'full';

    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $scheduledConfId = app()->getCurrentScheduledConferenceId();

        $metricsQuery = AnalyticMetric::query();
        $submissionQuery = Submission::query();

        if ($scheduledConfId) {
            $metricsQuery->where('scheduled_conference_id', $scheduledConfId);
            $submissionQuery->where('scheduled_conference_id', $scheduledConfId);
        }

        $totalAbstractViews = (int) (clone $metricsQuery)
            ->where('assoc_type', 3)
            ->sum('metric');

        $totalPdfDownloads = (int) (clone $metricsQuery)
            ->where('assoc_type', 4)
            ->sum('metric');

        $totalSubmissions = (int) $submissionQuery->count();

        return [
            Stat::make('Total Abstract Views', number_format($totalAbstractViews))
                ->description('Total paper abstract views')
                ->descriptionIcon('heroicon-o-eye'),

            Stat::make('Total Downloads', number_format($totalPdfDownloads))
                ->description('Total PDF galley downloads')
                ->descriptionIcon('heroicon-o-arrow-down-tray'),

            Stat::make('Total Papers', number_format($totalSubmissions))
                ->description('Total manuscripts & published papers')
                ->descriptionIcon('heroicon-o-document-text'),
        ];
    }
}
