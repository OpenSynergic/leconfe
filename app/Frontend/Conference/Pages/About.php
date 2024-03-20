<?php

namespace App\Frontend\Conference\Pages;

use Illuminate\Contracts\Support\Htmlable;
use Rahmanramsi\LivewirePageGroup\Pages\Page;

class About extends Page
{
    protected static string $view = 'frontend.conference.pages.about';

    public function mount()
    {
    }

    public function getTitle(): string|Htmlable
    {
        return 'About the Conference';
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }
}
