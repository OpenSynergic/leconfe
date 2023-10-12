<?php

namespace App\Conference\Pages;

use App\Models\Timeline as ConferenceTimeline;
use Rahmanramsi\LivewirePageGroup\Pages\Page;

class Timeline extends Page
{
    protected static string $view = 'conference.pages.timeline';

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
        return [
            'events' => ConferenceTimeline::with('conference')
                ->where('conference_id', app()->getCurrentConference()?->getKey())->get()
        ];
    }
}
