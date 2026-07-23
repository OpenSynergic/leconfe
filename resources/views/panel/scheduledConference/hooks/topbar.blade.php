<div class="hidden sm:flex flex-col ms-48">
    <a href="{{ app()->getCurrentScheduledConference()->getHomeUrl() }}" class="text-lg font-medium leading-none">{{ app()->getCurrentScheduledConference()->title }}</a>
    @hook('Panel::ScheduledConference::TopbarAfterTitle')
</div>


