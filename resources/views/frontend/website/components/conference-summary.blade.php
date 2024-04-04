@props(['conference'])

<div class="conference-summary sm:flex gap-4">
    <div class="cover max-w-40">
        <img class="" src="{{ $conference->getThumbnailUrl() }}" alt="{{ $conference->name }}">
    </div>
    <div class="information flex-1 space-y-2">
        <div class="flex items-center text-sm gap-2 text-gray-600">
            <x-heroicon-c-calendar-days class="h-5 w-5" />
            <span>
                {{ $conference->date_start?->format(setting('format.date')) }}
            </span>
        </div>
        <a href="{{ $conference->getHomeUrl() }}"
            class="conference-name link link-primary link-hover font-bold">{{ $conference->name }}</a>
        <p class="conference-description line-clamp-4 text-sm">{{ $conference->getMeta('description') }}</p>
        <a href="{{ $conference->getHomeUrl() }}" class="btn btn-primary btn-sm">Check Conference</a>
    </div>
</div>
