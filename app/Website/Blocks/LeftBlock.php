<?php

namespace App\Website\Blocks;

use App\Classes\Block;

class LeftBlock extends Block
{
    protected ?string $view = 'website.blocks.left-block';

    protected ?int $sort = 1;

    protected ?string $position = 'left';

    protected string $name = 'Left Block';
}
