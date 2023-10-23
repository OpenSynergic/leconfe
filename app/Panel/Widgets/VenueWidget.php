<?php

namespace App\Panel\Widgets;

use App\Models\Venue;
use Filament\Widgets\Widget;

class VenueWidget extends Widget
{
    protected static string $view = 'panel.widgets.venue-widget';

    protected static ?int $sort = 3;

    protected function getViewData(): array
    {
        $venue = Venue::limit(3)->get();

        return ['venue' => $venue];
    }
}
