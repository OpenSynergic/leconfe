<?php

namespace App\Panel\Resources\Conferences\SpeakerResource\Widgets;

use App\Models\Participants\Speaker;
use App\Models\Participants\SpeakerPosition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SpeakerOverview extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    protected function getColumns(): int
    {
        return 4;
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Total Speakers', Speaker::count()),
            Stat::make('Total Speaker Position', SpeakerPosition::count()),
        ];
    }
}
