<?php

namespace App\Frontend\ScheduledConference\Components;

use App\Models\AnalyticMetric;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AnalyticStatsChart extends Component
{
    public string $range = '6 Months'; // 6 Months, 12 Months, Overall

    public function setRange(string $range): void
    {
        $this->range = $range;
    }

    public function downloadCsv(): StreamedResponse
    {
        $fileName = 'analytic_metrics_' . date('Ymd_His') . '.csv';

        $query = AnalyticMetric::query();
        if ($scheduledConfId = app()->getCurrentScheduledConferenceId()) {
            $query->where('scheduled_conference_id', $scheduledConfId);
        }

        $metrics = $query->orderBy('day', 'desc')->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function () use ($metrics) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Load ID', 'Day', 'Month', 'Record Type', 'Assoc ID', 'Metric', 'Country', 'Region', 'City']);

            foreach ($metrics as $row) {
                fputcsv($file, [
                    $row->id,
                    $row->load_id,
                    $row->day,
                    $row->month,
                    $row->assoc_type == 4 ? 'PDF Galley Download' : 'Abstract View',
                    $row->assoc_id,
                    $row->metric,
                    $row->country_id,
                    $row->region,
                    $row->city,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function render()
    {
        $limitMonths = match ($this->range) {
            '12 Months' => 12,
            'Overall' => 36,
            default => 6,
        };

        $query = AnalyticMetric::selectRaw('month, assoc_type, SUM(metric) as total');
        if ($scheduledConfId = app()->getCurrentScheduledConferenceId()) {
            $query->where('scheduled_conference_id', $scheduledConfId);
        }

        $metrics = $query->groupBy('month', 'assoc_type')
            ->orderBy('month', 'asc')
            ->limit($limitMonths)
            ->get();

        $months = $metrics->pluck('month')->unique()->values()->toArray();
        $abstractViewsData = [];
        $galleyDownloadsData = [];

        foreach ($months as $month) {
            $abstractViewsData[] = (int)$metrics->where('month', $month)->where('assoc_type', '!=', 4)->sum('total');
            $galleyDownloadsData[] = (int)$metrics->where('month', $month)->where('assoc_type', 4)->sum('total');
        }

        return view('frontend.scheduledConference.components.analytic-stats-chart', [
            'months' => $months,
            'abstractViewsData' => $abstractViewsData,
            'galleyDownloadsData' => $galleyDownloadsData,
        ]);
    }
}
