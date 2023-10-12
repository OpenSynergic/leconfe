<?php

namespace App\Website\Blocks;

use App\Livewire\Block;
use App\Models\Scopes\ConferenceScope;
use App\Models\Topic;

class TopicBlock extends Block
{
    protected ?string $view = 'website.blocks.topic-block';

    protected ?int $sort = 3;

    protected string $name = 'Topic Block';

    protected ?string $position = 'right';

    public function getViewData(): array
    {
        return [
            'topics' => Topic::withoutGlobalScope(ConferenceScope::class)->get()
        ];
    }
}
