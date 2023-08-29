<?php

namespace App\Website\Blocks;

use App\Classes\Block;
use Illuminate\Contracts\View\View;

class ExampleBlock extends Block
{
    protected string | View | null $view = 'website.blocks.example-block';

    protected int | null $sort = 1;

    protected string | null $position = 'right';
}
