<?php

namespace App\Panel\Conference\Widgets;

use App\Models\Venue;
use Filament\Widgets\Widget;

class VenueWidget extends Widget
{
    protected static string $view = 'panel.conference.widgets.venue-widget';

    protected static ?int $sort = 3;

    protected function getViewData(): array
    {
        $venues = Venue::limit(3)->get();

        return ['venues' => $venues];
    }
}
