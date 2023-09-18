<?php

namespace App\Conference\Pages;

use Rahmanramsi\LivewirePageGroup\Pages\Page;

class About extends Page
{
    protected static string $view = 'conference.pages.about';

    public function mount()
    {
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }
}
