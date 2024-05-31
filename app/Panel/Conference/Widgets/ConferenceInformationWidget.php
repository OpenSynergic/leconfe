<?php

namespace App\Panel\Conference\Widgets;

use App\Models\Announcement;
use Filament\Widgets\Widget;

class ConferenceInformationWidget extends Widget
{
    protected static string $view = 'panel.conference.widgets.conference-information-widget';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 2;

    protected function getViewData(): array
    {
        return [
            'announcement' => Announcement::latest()->first(),
        ];
    }
}
