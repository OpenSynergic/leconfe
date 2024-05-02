<?php

namespace App\Frontend\Conference\Pages;

use App\Models\CommitteeRole;
use Rahmanramsi\LivewirePageGroup\Pages\Page;

class Committe extends Page
{
    protected static string $view = 'frontend.conference.pages.committe';

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
        $committeeRole = CommitteeRole::query()
            ->with(['committees' => fn ($query) => $query->orderBy('order_column')])
            ->get();

        return [
            'groupedCommittes' => $committeeRole,
        ];
    }
}
