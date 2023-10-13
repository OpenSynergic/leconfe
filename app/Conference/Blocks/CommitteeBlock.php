<?php

namespace App\Conference\Blocks;

use App\Livewire\Block;
use App\Models\Participant;

class CommitteeBlock extends Block
{
    protected ?string $view = 'conference.blocks.committe-block';

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
            // Return the organized data with committee positions as keys and arrays of participants as values.
            'participants' => $participants,
        ];
    }
}
