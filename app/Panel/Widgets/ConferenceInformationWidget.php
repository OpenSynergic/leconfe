<?php

namespace App\Panel\Widgets;

use App\Models\Announcement;
use Filament\Widgets\Widget;

class ConferenceInformationWidget extends Widget
{
    protected static string $view = 'panel.widgets.conference-information-widget';

    protected int|string|array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $announcement = Announcement::latest()->first();

        return  ['announcement' => $announcement];
    }
    
}

