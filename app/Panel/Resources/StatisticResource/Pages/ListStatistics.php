<?php

namespace App\Panel\Resources\StatisticResource\Pages;

use App\Panel\Resources\StatisticResource;
use App\Panel\Resources\StatisticResource\Widgets\ConferenceOverviewWidget;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStatistics extends ListRecords
{
    protected static string $resource = StatisticResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getBreadcrumb(): ?string
    {
        return '';
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ConferenceOverviewWidget::class,
        ];
    }
}
