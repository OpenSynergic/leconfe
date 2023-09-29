<?php

namespace App\Website\Blocks;

use App\Livewire\Block;
use App\Models\Conference;
use App\Models\Timeline;

class TimelineBlock extends Block
{
    protected ?string $view = 'website.blocks.timeline-block';

    protected ?int $sort = 2;

    protected string $name = 'Timeline Block';

    protected ?string $position = 'left';

    public function getViewData(): array
    {
        return [
            'timelines' => Timeline::getTimelinesForCurrentConference()->get()
        ];
    }
}
