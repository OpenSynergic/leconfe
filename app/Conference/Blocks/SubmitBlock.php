<?php

namespace App\Conference\Blocks;

use App\Livewire\Block;

class SubmitBlock extends Block
{
    protected ?string $view = 'conference.blocks.submit-block';

    protected ?int $sort = 1;

    protected string $name = 'Submit Block';

    protected ?string $position = 'right';

    public function getViewData(): array
    {
        return [];
    }
}
