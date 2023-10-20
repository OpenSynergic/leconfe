<?php

namespace App\Panel\Widgets;

use App\Models\Timeline;
use Filament\Widgets\Widget;

class TimelineWidget extends Widget
{
    protected static string $view = 'panel.widgets.timeline-widget';

    protected static ?int $sort = 1;

    protected function getViewData(): array
    {
        $timeline = Timeline::all();
    
        return ['timeline' => $timeline];

  

    }
}
