<?php

namespace App\Frontend\Conference\Blocks;

use App\Classes\Block;
use App\Models\Committee;

class CommitteeBlock extends Block
{
    protected ?string $view = 'frontend.conference.blocks.committe-block';

    protected ?int $sort = 4;

    protected string $name = 'Committee Block';

    protected ?string $position = 'right';

    public function getViewData(): array
    {
        $committees = Committee::query()
            ->orderBy('order_column')
            ->take(3)
            ->get();

        return [
            ...parent::getViewData(),
            'committees' => $committees,
        ];
    }
}
