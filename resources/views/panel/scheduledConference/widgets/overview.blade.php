<x-filament-widgets::widget class="scheduled-conference-overview">
    @hook('Panel::ScheduledConference::DashboardOverviewBefore')

    {{ $this->scheduledConferenceInfolist }}

    @hook('Panel::ScheduledConference::DashboardOverviewAfter')

    <x-filament-actions::modals />
</x-filament-widgets::widget>
