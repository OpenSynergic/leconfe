<?php

namespace App\Website\Blocks;

use App\Livewire\Block;
use App\Models\Conference;

class InformationBlock extends Block
{
    protected ?string $view = 'website.blocks.information-block';

    protected ?int $sort = 4;

    protected string $name = 'Information Block';

    protected ?string $position = 'right';

    public function getViewData(): array
    {
        return [];
    }
}