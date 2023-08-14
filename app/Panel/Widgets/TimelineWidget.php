<?php

namespace App\Panel\Widgets;

use Filament\Widgets\Widget;

class TimelineWidget extends Widget
{
    protected static string $view = 'panel.widgets.timeline-widget';

    protected static ?int $sort = 1;
}
