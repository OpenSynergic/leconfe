<?php

namespace App\Panel\ScheduledConference\Widgets;

use App\Models\AnalyticMetric;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class AnalyticStatsLineChartWidget extends ChartWidget
{
    protected static bool $isDiscovered = false;

    protected ?string $heading = 'Activity Overview';

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $scheduledConfId = app()->getCurrentScheduledConferenceId();

        $startDate = Carbon::now()->subDays(30);

        $viewsData = [];
        $downloadsData = [];
        $labels = [];

        for ($i = 0; $i <= 30; $i++) {
            $date = (clone $startDate)->addDays($i);
            $dateStr = $date->format('Ymd');
            $labels[] = $date->format('M j, Y');

            $dayViews = AnalyticMetric::query()
                ->when($scheduledConfId, fn ($q) => $q->where('scheduled_conference_id', $scheduledConfId))
                ->where('day', $dateStr)
                ->where('assoc_type', 3)
                ->sum('metric');

            $dayDownloads = AnalyticMetric::query()
                ->when($scheduledConfId, fn ($q) => $q->where('scheduled_conference_id', $scheduledConfId))
                ->where('day', $dateStr)
                ->where('assoc_type', 4)
                ->sum('metric');

            $viewsData[] = (int) $dayViews;
            $downloadsData[] = (int) $dayDownloads;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Abstract Views',
                    'data' => $viewsData,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Downloads',
                    'data' => $downloadsData,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        $scheduledConfId = app()->getCurrentScheduledConferenceId();
        $hasData = AnalyticMetric::query()
            ->when($scheduledConfId, fn ($q) => $q->where('scheduled_conference_id', $scheduledConfId))
            ->whereIn('assoc_type', [3, 4])
            ->where('metric', '>', 0)
            ->exists();

        $yScale = [
            'beginAtZero' => true,
            'ticks' => [
                'precision' => 0,
            ],
        ];

        if (! $hasData) {
            $yScale['suggestedMax'] = 1;
            $yScale['ticks']['maxTicksLimit'] = 1;
        }

        return [
            'scales' => [
                'y' => $yScale,
            ],
        ];
    }
}
