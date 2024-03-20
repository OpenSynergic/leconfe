<?php

namespace App\Frontend\Conference\Blocks;

use App\Classes\Block;

class SubmitBlock extends Block
{
    protected ?string $view = 'frontend.conference.blocks.submit-block';

    protected ?int $sort = 1;

    protected string $name = 'Submit Block';

    protected ?string $position = 'right';
}
