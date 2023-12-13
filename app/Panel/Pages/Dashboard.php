<?php

namespace App\Panel\Pages;

use App\Panel\Widgets\ConferenceInformationWidget;
use App\Panel\Widgets\ParticipanSubmissionWidget;
use App\Panel\Widgets\SpeakerWidget;
use App\Panel\Widgets\TimelineWidget;
use App\Panel\Widgets\VenueWidget;

class Dashboard extends \Filament\Pages\Dashboard
{
    public function getWidgets(): array
    {
        return [
            ConferenceInformationWidget::class,
            ParticipanSubmissionWidget::class,
            SpeakerWidget::class,
            TimelineWidget::class,
            VenueWidget::class
        ];
    }
}
