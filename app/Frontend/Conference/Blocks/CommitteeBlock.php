<?php

namespace App\Frontend\Conference\Blocks;

use App\Classes\Block;
use App\Models\Participant;

class CommitteeBlock extends Block
{
    protected ?string $view = 'frontend.conference.blocks.committe-block';

    protected ?int $sort = 4;

    protected string $name = 'Committee Block';

    protected ?string $position = 'right';

    public function getViewData(): array
    {
        $participants = Participant::with('positions')
            ->whereHas('positions', function ($query) {
                // Filter participants to include only those with 'committee' type positions.
                $query->where('type', 'committee');
            })
            ->orderBy('order_column') // Order the retrieved data by the 'order_column'.
            ->take(3) // Limit the query to retrieve only 3 participants.
            ->get();

        return [
            ...parent::getViewData(),
            'participants' => $participants,
        ];
    }
}
