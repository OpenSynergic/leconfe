<?php

namespace App\Panel\Conference\Pages;

use App\Models\Enums\DOIStatus;
use Filament\Pages\Page;

class DOIPage extends Page
{
    protected static ?string $navigationIcon = 'academicon-doi';

    protected static string $view = 'panel.conference.pages.doi-page';

    protected static ?string $title = 'DOIs';

    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        return app()->getCurrentConference()?->getMeta('doi_enabled') && !empty(app()->getCurrentConference()?->getMeta('doi_items', []));
    }

    public function mount()
    {
    }

    public function getViewData(): array
    {
        return [
            'articlesDoiEnabled' => in_array('articles', app()->getCurrentConference()?->getMeta('doi_items', [])),
            'proceedingsDoiEnabled' => in_array('proceedings', app()->getCurrentConference()?->getMeta('doi_items', [])),
        ];
    }

    public static function getRoutePath(): string
    {
        return '/dois';
    }
}

