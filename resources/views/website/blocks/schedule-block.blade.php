<div class="flex flex-col space-y-1">
    @if (count($upcomings) > 0)
    <h2 class="text-heading px-2 mb-1">Schedule</h2>
    @foreach ($upcomings as $upcoming)
    <div class="tooltip upcoming-timeline text-start" data-tip="{{ $upcoming->status }}">
        <div class="w-full flex justify-between">
           @if ($upcoming->hasMeta('date_held'))
           <div class="inline-flex items-center gap-2">
            <div class="upcoming-marker"></div>
            <time class="text-xs">{{ date(setting('format.date'), strtotime($upcoming->getMeta('date_held'))) }}</time>
            </div>
           @endif

            <div class="flex flex-wrap gap-1">
                <span class="offline-badge">{{ $upcoming->type ?? '' }}</span>
            </div>
        </div>

        <div class="flex flex-col gap-2 mt-2">
            <h5 class="text-sm">{{ $upcoming->name }}</h5>
            @if ($upcoming->hasMeta('location'))
            <span class="text-xs -mt-1 break-all">{{ $upcoming->getMeta('location') }}</span>
            @endif
        </div>
    </div>
    @endforeach
    @endif
</div>