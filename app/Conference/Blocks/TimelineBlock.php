<?php

namespace App\Conference\Blocks;

use App\Livewire\Block;
use App\Models\Timeline;
use Carbon\Carbon;

class TimelineBlock extends Block
{
    protected ?string $view = 'conference.blocks.timeline-block';

    protected ?int $sort = 2;

    protected string $name = 'Timeline Block';

    protected ?string $position = 'left';

    public function getViewData(): array
    {
        $today = Carbon::now();

        $timelines = Timeline::where('conference_id', app()->getCurrentConference()?->getKey())
            ->where(function ($query) use ($today) {
                $query->where('date', $today->subDay()->toDateString())
                    ->orWhereBetween('date', [
                        $today->addDays(1)->toDateString(), // Today
                        $today->addDays(2)->toDateString(), // 2 days ahead
                    ]);
            })
            ->orWhere(function ($query) {
                $query->latest()->limit(3);
            })
            ->orderBy('date')
            ->get();

        $timelineData = [];

        foreach ($timelines as $timeline) {
            $background = match (true) {
                $timeline->date <= now()->subDay() => 'past-timeline',
                $timeline->date->isToday() => 'current-timeline',
                default => 'upcoming-timeline',
            };

            $marker = match (true) {
                $timeline->date <= now()->subDay() => 'past-marker',
                $timeline->date->isToday() => 'current-marker',
                default => 'upcoming-marker',
            };

            $badgeRoles = [];

            $countRole = count($timeline->roles);

            foreach ($timeline->roles as $key => $role) {
                $badgeRole = match ($role) {
                    'Author' => 'author-badge',
                    'Editor' => 'editor-badge',
                    'Reviewer' => 'reviewer-badge',
                    'Participant' => 'participant-badge',
                    default => 'participant-badge',
                };

                if ($key < 1) {
                    $badgeRoles[] = [
                        'badgeRole' => $badgeRole,
                        'role' => $role,
                        'moreCount' => $countRole - 1,
                    ];
                }
            }

            $timelineData[] = [
                'timeline' => $timeline,
                'timelineBackground' => $background,
                'timelineMarker' => $marker,
                'badgeRoles' => $badgeRoles,
            ];
        }

        return [
            'timelines' => $timelineData,
        ];
    }
}
