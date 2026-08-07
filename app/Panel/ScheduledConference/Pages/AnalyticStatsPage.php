<?php

namespace App\Panel\ScheduledConference\Pages;

use App\Panel\ScheduledConference\Widgets\AnalyticStatsLineChartWidget;
use App\Panel\ScheduledConference\Widgets\AnalyticStatsOverviewWidget;
use App\Panel\ScheduledConference\Widgets\SubmissionDetailsTableWidget;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class AnalyticStatsPage extends Page
{
    protected string $view = 'panel.scheduledConference.pages.analytic-stats-page';

    public static function getNavigationGroup(): ?string
    {
        return 'Reports & Analytics';
    }

    public static function getNavigationSort(): ?int
    {
        return 99;
    }

    public static function getNavigationLabel(): string
    {
        return 'AnalyticStats';
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-presentation-chart-line';
    }

    public function getTitle(): string|Htmlable
    {
        return 'AnalyticStats & Readership';
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }

        if (method_exists($user, 'isAdministrator') && $user->isAdministrator()) {
            return true;
        }

        $scheduledConference = app()->getCurrentScheduledConference();

        return $scheduledConference ? $user->can('update', $scheduledConference) : false;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            AnalyticStatsOverviewWidget::class,
            AnalyticStatsLineChartWidget::class,
            SubmissionDetailsTableWidget::class,
        ];
    }
}
