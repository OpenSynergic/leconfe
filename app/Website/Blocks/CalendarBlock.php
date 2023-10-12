<?php

namespace App\Website\Blocks;

use App\Livewire\Block;
use App\Models\Conference;
use Carbon\Carbon;

class CalendarBlock extends Block
{
    protected ?string $view = 'website.blocks.calendar-block';

    protected ?int $sort = 1;

    protected string $name = 'Calendar Block';

    protected ?string $position = 'left';

    public function getViewData(): array
    {
        $upcomings = Conference::upcoming()->get();

        $formattedUpcomings = [];

        foreach ($upcomings as $upcoming) {
            $upcomingDate = Carbon::parse($upcoming->getMeta('date_held'))->format('Y-m-d');

            $formattedUpcomings[$upcomingDate] = [
                'modifier' => 'upcoming_timeline',
                'html' => $upcoming->name
            ];
        }
        return [
            'upcomings' => $formattedUpcomings
        ];
    }
}
