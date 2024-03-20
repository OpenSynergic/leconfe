<?php

namespace App\Frontend\Conference\Blocks;

use App\Classes\Block;
use App\Models\Topic;

class TopicBlock extends Block
{
    protected ?string $view = 'frontend.conference.blocks.topic-block';

    protected ?int $sort = 3;

    protected string $name = 'Topic Block';

    protected ?string $position = 'right';

    public function getViewData(): array
    {
        return [
            'topics' => Topic::where('conference_id', app()->getCurrentConference()?->getKey())->get(),
        ];
    }
}
