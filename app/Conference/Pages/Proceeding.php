<?php

namespace App\Conference\Pages;

use App\Models\Topic;
use Rahmanramsi\LivewirePageGroup\Pages\Page;

class Proceeding extends Page
{
    protected static string $view = 'conference.pages.proceeding';

    public function mount()
    {
    }

    public function getViewData(): array
    {
        return [
            'topics' => Topic::get()
        ];
    }
}
