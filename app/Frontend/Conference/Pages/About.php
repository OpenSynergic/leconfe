<?php

namespace App\Frontend\Conference\Pages;

use Livewire\Attributes\Title;
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

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        return [
            'about' => app()->getCurrentConference()?->getMeta('about')
        ];
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }
}
