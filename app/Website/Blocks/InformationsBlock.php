<?php

namespace App\Website\Blocks;

use App\Livewire\Block;
use App\Models\Conference;

class InformationsBlock extends Block
{
    protected ?string $view = 'website.blocks.informations-block';

    protected ?int $sort = 3;

    protected string $name = 'Informations Block';

    protected ?string $position = 'left';

    public function getViewData(): array
    {
        return [];
    }
}
