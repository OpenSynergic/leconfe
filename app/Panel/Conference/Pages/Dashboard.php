<?php

namespace App\Panel\Conference\Pages;

use App\Panel\Conference\Widgets;
use Filament\Pages\Dashboard as PagesDashboard;

class Dashboard extends PagesDashboard
{
    public function mount()
    {
    }

    public function getWidgetForNewConferenceUser()
    {
        $userConferenceRole = auth()->user()->roles->pluck('name')->toArray();

        return !empty($userConferenceRole) ? [] : [
            Widgets\NewUserConferenceRegisterWidget::class,
        ];
    }

    public function getWidgets(): array
    {
        return [
            ...$this->getWidgetForNewConferenceUser(),
            Widgets\ConferenceInformationWidget::class,
            Widgets\ParticipanSubmissionWidget::class,
        ];
    }
}
