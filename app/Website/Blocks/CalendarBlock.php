<?php

namespace App\Website\Blocks;

use App\Livewire\Block;

class CalendarBlock extends Block
{
    protected ?string $view = 'website.blocks.calendar-block';

    protected ?int $sort = 1;

    protected string $name = 'Calendar Block';

    protected ?string $position = 'left';
}
