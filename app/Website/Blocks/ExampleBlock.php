<?php

namespace App\Website\Blocks;

use App\Classes\Block;
use Illuminate\Contracts\View\View;

class ExampleBlock extends Block
{
    protected static string | View | null $view = 'website.blocks.example-block';

    protected static int | null $sort = 1;

    protected static string | null $position = 'right';
}
