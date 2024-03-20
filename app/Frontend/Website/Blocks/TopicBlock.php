<?php

namespace App\Frontend\Website\Blocks;

use App\Classes\Block;
use App\Models\Scopes\ConferenceScope;
use App\Models\Topic;

class TopicBlock extends Block
{
    protected ?string $view = 'frontend.website.blocks.topic-block';

    protected ?int $sort = 3;

    protected string $name = 'Topic Block';

    protected ?string $position = 'right';

    public function getViewData(): array
    {
        return [
            'topics' => Topic::withoutGlobalScope(ConferenceScope::class)
                ->latest('created_at')
                ->limit(10)
                ->get(),
        ];
    }
}
