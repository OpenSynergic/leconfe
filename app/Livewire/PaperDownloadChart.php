<?php

namespace App\Livewire;

use App\Models\AnalyticMetric;
use App\Models\Submission;
use Livewire\Component;

class PaperDownloadChart extends Component
{
    public Submission $submission;

    public function render()
    {
        $scheduledConf = $this->submission->scheduledConference ?? app()->getCurrentScheduledConference();
        $rawMeta = $scheduledConf ? $scheduledConf->getMeta('display_reader_statistics') : null;

        $isEnabled = $rawMeta === null ? true : filter_var($rawMeta, FILTER_VALIDATE_BOOLEAN);

        if (!$isEnabled) {
            return view('livewire.paper-download-chart', [
                'isEnabled' => false,
                'labels' => [],
                'values' => [],
            ]);
        }

        $downloadCounts = AnalyticMetric::where('submission_id', $this->submission->id)
            ->where(function ($q) {
                $q->where('assoc_type', 4)->orWhere('file_type', '>', 0);
            })
            ->selectRaw('month, SUM(metric) as total')
            ->groupBy('month')
            ->pluck('total', 'month');

        $labels = [];
        $values = [];
        for ($i = 11; $i >= 0; $i--) {
            $d = now()->subMonths($i);
            $mKey = $d->format('Ym');
            $labels[] = $d->format('M');
            $values[] = (int)($downloadCounts[$mKey] ?? 0);
        }

        return view('livewire.paper-download-chart', [
            'isEnabled' => true,
            'labels' => $labels,
            'values' => $values,
        ]);
    }
}
