<div class="flex flex-col space-y-3">
    @if (!empty($schedules))
    <h2 class="text-heading px-2">Scheduled Date</h2>
    @foreach ($schedules as $schedule)

    <div class="card card-compact bg-white border w-full p-3 flex-col rounded">
        <div class="w-full flex justify-between">
            @if ($schedule->hasMeta('date_held'))
            <div class="inline-flex items-center gap-2">
                <div class="w-2 h-2 rounded-full bg-yellow-400"></div>
                <time class="small-text">{{ date('d M Y', strtotime($schedule->getMeta('date_held'))) }}</time>
            </div>
            @endif
            <span class="badge badge-outline text-mini badge-xs w-16 h-5 text-yellow-400">Author</span>
        </div>

        <div class="flex flex-col gap-2 mt-2">
            <h5 class="text-subheading">{{ $schedule->name }}</h5>
            @if ($schedule->hasMeta('location'))
            <span class="small-text -mt-1">{{ $schedule->getMeta('location') }}</span>

            @endif
        </div>
    </div>

    @endforeach
    @endif
</div>
