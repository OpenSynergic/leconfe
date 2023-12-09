<div class="flex flex-col space-y-1">
    @if (count($timelines) > 0)
        <h2 class="text-heading px-2 mb-1">Informations</h2>
        @foreach ($timelines as $timeline)
            <div class="{{ $timeline['timelineBackground'] }}">
                <div class="w-full flex justify-between">
                    <div class="inline-flex items-center gap-2">
                        <div class="{{ $timeline['timelineMarker'] }}"></div>
                        <time class="text-xs">{{ date(setting('format.date'), strtotime($timeline['timeline']->date)) }}</time>
                    </div>
                    <div class="flex flex-wrap gap-1">
                        @foreach ($timeline['badgeRoles'] as $badgeRole)
                            <div class="inline-flex gap-1">
                                <span class="{{ $badgeRole['badgeRole'] }}">
                                    {{ $badgeRole['role'] }}
                                </span>
                                @if ($badgeRole['moreCount'] > 0)
                                    <span class="badge badge-outline text-2xs badge-xs w-16 h-5 text-gray-400">+{{ $badgeRole['moreCount'] }} more</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="flex flex-col gap-2 mt-2">
                    <h5 class="text-sm">{{ Str::words($timeline['timeline']->title, 3, '...') }}</h5>
                    <span class="text-xs -mt-1 break-all">{{ $timeline['timeline']->subtitle ?? '' }}</span>
                </div>
            </div>
        @endforeach
        <div class="w-full flex justify-start pt-1">
            <a href="{{ route('livewirePageGroup.current-conference.pages.timelines') }}"
                class="btn btn-primary text-xs btn-sm text-white rounded-md w-16">More</a>
        </div>
    @endif
</div>
