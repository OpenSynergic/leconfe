<?php

namespace App\Conference\Blocks;

use Carbon\Carbon;
use App\Livewire\Block;
use App\Models\Timeline;

class TimelineBlock extends Block
{
    protected ?string $view = 'conference.blocks.timeline-block';

    protected ?int $sort = 2;

    protected string $name = 'Timeline Block';

    protected ?string $position = 'left';


    public function getViewData(): array
    {
        $today = Carbon::now();

        $timelines = Timeline::where('conference_id', app()->getCurrentConference()?->getKey())
            ->where(function ($query) use ($today) {
                // 1 day before now (yesterday)
                $query->where('date', $today->subDay()->toDateString())
                    ->orWhereBetween('date', [
                        $today->addDays(1)->toDateString(), // Today
                        $today->addDays(2)->toDateString(), // 2 days ahead
                    ]);
            })
            ->orWhere(function ($query) {
                // If no data matches the above criteria, display only the latest 3 data
                $query->latest()->limit(3);
            })
            ->orderBy('date')
            ->get();

        return [
            'timelines' => $timelines,
        ];
    }
}
