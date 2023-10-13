<?php

namespace App\Conference\Blocks;

use App\Livewire\Block;
use App\Models\Conference;
use App\Models\Enums\ConferenceStatus;

class PreviousBlock extends Block
{
    protected ?string $view = 'conference.blocks.previous-block';

    protected ?int $sort = 3;

    protected string $name = 'Previous Block';

    protected ?string $position = 'left';

    public function getViewData(): array
    {
        return [
            'archives' => Conference::where('status', ConferenceStatus::Archived)->get(),
        ];
    }
}
