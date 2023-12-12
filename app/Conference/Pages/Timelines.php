<?php

namespace App\Conference\Pages;

use App\Models\Timeline;
use Rahmanramsi\LivewirePageGroup\Pages\Page;

class Timelines extends Page
{
    protected static string $view = 'conference.pages.timelines';

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
            'timelines' => Timeline::with('conference')->get(),
        ];
    }
}
