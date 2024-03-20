<?php

namespace App\Frontend\Website\Blocks;

use App\Classes\Block;

class SearchBlock extends Block
{
    protected ?string $view = 'frontend.website.blocks.search-block';

    protected ?int $sort = 1;

    protected string $name = 'Search Block';

    protected ?string $position = 'right';

    public function getViewData(): array
    {
        return [];
    }
}
