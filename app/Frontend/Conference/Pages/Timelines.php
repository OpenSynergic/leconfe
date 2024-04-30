<?php

namespace App\Frontend\Conference\Pages;

use App\Models\Timeline;
use Rahmanramsi\LivewirePageGroup\Pages\Page;

class Timelines extends Page
{
    protected static string $view = 'frontend.conference.pages.timelines';

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

    protected function getLayoutData(): array
    {
        return [
            'title' => $this->getTitle()
        ];
    }
}
