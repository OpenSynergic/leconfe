<?php

namespace App\Panel\Widgets;

use App\Models\Conference;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

use function Filament\Support\format_number;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Visit Website', $this->getTotalVisit()),
        ];
    }

    public function getTotalVisit() : int 

    {
        return format_number(Conference::withTotalVisitCount()->first()->visit_count_total); 
    }

    
}
