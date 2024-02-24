<?php

namespace App\Website\Blocks;

use App\Classes\Block;

class ExampleBlock extends Block
{
    protected ?string $view = 'website.blocks.example-block';

    protected ?int $sort = 1;

    protected string $name = 'Example Block';

    protected ?string $position = 'right';
}
