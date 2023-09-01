<?php

namespace App\Website\Blocks;

use App\Livewire\Block;
use Illuminate\Contracts\View\View;

class ExampleBlock extends Block
{
    protected string | null $view = 'website.blocks.example-block';

    protected int | null $sort = 1;

    protected string | null $position = 'right';
}
