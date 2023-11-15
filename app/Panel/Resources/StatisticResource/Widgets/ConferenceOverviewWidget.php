<?php

namespace App\Panel\Resources\StatisticResource\Widgets;

use App\Models\User;
use App\Models\Conference;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class ConferenceOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalUser = $this->getTotalUser();
        $totalVisit = $this->getTotalVisit();
        return [
            Stat::make('Total visits website', $totalVisit),
            Stat::make('Total users', $totalUser)
        ];
    }

    protected function getTotalUser()
    {
        return User::count();
    }

    protected function getTotalVisit()
    {
        $totalVisit = Conference::withTotalVisitCount()->first()->visit_count_total;

        return number_format($totalVisit);
    }
}
