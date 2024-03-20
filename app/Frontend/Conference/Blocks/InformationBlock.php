<?php

namespace App\Frontend\Conference\Blocks;

use App\Classes\Block;

class InformationBlock extends Block
{
    protected ?string $view = 'frontend.conference.blocks.information-block';

    protected ?int $sort = 4;

    protected string $name = 'Information Block';

    protected ?string $position = 'right';
}
