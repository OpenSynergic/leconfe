<?php

namespace App\Website\Blocks;

use App\Livewire\Block;
use App\Models\Conference;

class LoginBlock extends Block
{
    protected ?string $view = 'website.blocks.login-block';

    protected ?int $sort = 2;

    protected string $name = 'Login Block';

    protected ?string $position = 'right';

    public function getViewData(): array
    {
        return [];
    }
}
