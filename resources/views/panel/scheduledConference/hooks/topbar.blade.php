<div class="hidden sm:flex flex-col ms-48">
    <span class="text-xs text-gray-500 font-medium leading-none mb-1">{{ app()->getCurrentConference()->name }}</span>
    <a href="{{ app()->getCurrentScheduledConference()->getHomeUrl() }}" class="text-lg font-medium leading-none">{{ app()->getCurrentScheduledConference()->title }}</a>
    @hook('Panel::ScheduledConference::TopbarAfterTitle')
</div>
