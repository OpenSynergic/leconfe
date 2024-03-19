<?php

namespace App\Frontend\Website\Blocks;

use App\Classes\Block;

class ExampleBlock extends Block
{
    protected ?string $view = 'frontend.website.blocks.example-block';

    protected ?int $sort = 1;

    protected string $name = 'Example Block';

    protected ?string $position = 'right';
}
