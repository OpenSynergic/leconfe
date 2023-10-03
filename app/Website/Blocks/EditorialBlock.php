<?php

namespace App\Website\Blocks;

use App\Livewire\Block;

class EditorialBlock extends Block
{
    protected ?string $view = 'website.blocks.editorial-block';

    protected ?int $sort = 6;

    protected string $name = 'Editorial Block';

    protected ?string $position = 'right';
}
