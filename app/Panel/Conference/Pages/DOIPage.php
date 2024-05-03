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
        return app()->getCurrentConference()->getMeta('doi_enabled');
    }

    public function mount()
    {
    }

    public static function getRoutePath(): string
    {
        return '/dois';
    }
}

