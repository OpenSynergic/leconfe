<?php

namespace App\Website\Blocks;

use App\Livewire\Block;
use App\Models\Conference;

class ScheduleBlock extends Block
{
    protected ?string $view = 'website.blocks.schedule-block';

    protected ?int $sort = 3;

    protected string $name = 'Schedule Block';

    protected ?string $position = 'left';

    public function getViewData(): array
    {
        return [
            'upcomings' => Conference::upcoming()->get(),
        ];
    }
}
