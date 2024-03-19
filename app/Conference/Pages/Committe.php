<?php

namespace App\Conference\Pages;

use App\Models\Participant;
use App\Panel\Conference\Resources\Conferences\CommitteePositionResource;
use Rahmanramsi\LivewirePageGroup\Pages\Page;

class Committe extends Page
{
    protected static string $view = 'conference.pages.committe';

    public function mount()
    {
        //
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }

    protected function getViewData(): array
    {
        // Retrieve participants with their associated committee positions.
        $participants = Participant::with([
            'positions' => fn ($query) => $query->where('type', CommitteePositionResource::$positionType),
        ])
            ->orderBy('order_column') // Order the retrieved data by the 'order_column'.
            ->get();

        // Initialize an empty associative array to store organized data.
        $groupedData = [];

        // Iterate through each participant and their associated committee positions.
        foreach ($participants as $participant) {
            foreach ($participant->positions as $position) {
                $positionName = $position->name;
                if (! isset($groupedData[$positionName])) {
                    // Create an array for the committee position if it doesn't exist in $groupedData.
                    $groupedData[$positionName] = [];
                }
                // Add the participant to the respective committee position in $groupedData.
                $groupedData[$positionName][] = $participant;
            }
        }

        return [
            // Return the organized data with committee positions as keys and arrays of participants as values.
            'groupedCommittes' => $groupedData,
        ];
    }
}
