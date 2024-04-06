<?php

namespace App\Frontend\Conference\Blocks;

use App\Classes\Block;
use App\Models\Conference;
use App\Models\Enums\ConferenceStatus;

class PreviousBlock extends Block
{
    protected ?string $view = 'frontend.conference.blocks.previous-block';

    protected ?int $sort = 3;

    protected string $name = 'Previous Block';

    protected ?string $position = 'left';

    public function getViewData(): array
    {
        return [
            ...parent::getViewData(),
            'archives' => Conference::archived()->get(),
        ];
    }
}
