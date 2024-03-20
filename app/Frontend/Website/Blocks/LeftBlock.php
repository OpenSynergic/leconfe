<?php

namespace App\Frontend\Website\Blocks;

use App\Classes\Block;

class LeftBlock extends Block
{
    protected ?string $view = 'frontend.website.blocks.left-block';

    protected ?int $sort = 1;

    protected ?string $position = 'left';

    protected string $name = 'Left Block';
}
