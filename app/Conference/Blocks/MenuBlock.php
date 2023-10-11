<?php

namespace App\Conference\Blocks;

use App\Livewire\Block;
use App\Models\UserContent;

class MenuBlock extends Block
{
    protected ?string $view = 'conference.blocks.menu-block';

    protected ?int $sort = 2;

    protected string $name = 'Menu Block';

    protected ?string $position = 'right';

    public function getViewData(): array
    {
        return [
            'userContent' => UserContent::where('conference_id', app()->getCurrentConference()?->getKey())->get()
        ];
    }
}
