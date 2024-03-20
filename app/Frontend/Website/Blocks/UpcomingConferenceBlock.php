<?php

namespace App\Frontend\Website\Blocks;

use App\Classes\Block;
use App\Models\Conference;

class UpcomingConferenceBlock extends Block
{
    protected ?string $view = 'frontend.website.blocks.upcoming-block';

    protected ?int $sort = 3;

    protected string $name = 'Schedule Block';

    protected ?string $position = 'left';

    public function getViewData(): array
    {
        return [
            'upcomings' => Conference::upcoming()->limit(5)->get(),
        ];
    }
}
