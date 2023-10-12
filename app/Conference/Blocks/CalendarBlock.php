<?php

namespace App\Conference\Blocks;

use App\Livewire\Block;
use App\Models\Timeline;

class CalendarBlock extends Block
{
    protected ?string $view = 'conference.blocks.calendar-block';

    protected ?int $sort = 1;

    protected string $name = 'Calendar Block';

    protected ?string $position = 'left';

    public function getViewData(): array
    {
        // Retrieve timeline data and format it for the calendar
        $timelines = Timeline::where('conference_id', app()->getCurrentConference()?->getKey())->get();

        $formattedTimelines = [];

        foreach ($timelines as $timeline) {
            $timelineDate = $timeline->date->format('Y-m-d');

            $today = now()->format('Y-m-d');
            $tommorow = now()->addDay()->format('Y-m-d');

            $modifier = match (true) {
                $timelineDate === $today => 'current_timeline',
                $timelineDate >= $tommorow => 'upcoming_timeline',
                default => 'past_timeline'
            };

            $formattedTimelines[$timeline->date->format('Y-m-d')] = [
                'modifier' => $modifier,
                'html' => $timeline->title
            ];
        }
        return [
            'timelines' => $formattedTimelines
        ];
    }
}
