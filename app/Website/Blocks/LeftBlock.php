<?php

namespace App\Website\Blocks;

use App\Livewire\Block;
use Illuminate\Contracts\View\View;

class LeftBlock extends Block
{
    protected string | null $view = 'website.blocks.left-block';

    protected int | null $sort = 1;

    protected string | null $position = 'left';

    protected string | null $name = "Left Block";
}
