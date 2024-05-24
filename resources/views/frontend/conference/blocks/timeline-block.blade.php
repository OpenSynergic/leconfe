<x-block :id="$id" class="flex flex-col space-y-1">
    @if (count($timelines) > 0)
        <h2 class="text-heading mb-1 px-2">Timelines</h2>
        @foreach ($timelines as $timeline)
            <div class="{{ $timeline['timelineBackground'] }}">
                <div class="flex w-full justify-between">
                    <div class="inline-flex items-center gap-2">
                        <div class="{{ $timeline['timelineMarker'] }}"></div>
                        <time class="text-xs">
                            {{ date(Setting::get('format_date'), strtotime($timeline['timeline']->date)) }}
                        </time>
                    </div>
                    <div class="flex flex-wrap gap-1">
                        @foreach ($timeline['badgeRoles'] as $badgeRole)
                            <div class="inline-flex gap-1">
                                <span class="{{ $badgeRole['badgeRole'] }}">
                                    {{ $badgeRole['role'] }}
                                </span>
                                @if ($badgeRole['moreCount'] > 0)
                                    <span
                                        class="badge badge-outline text-2xs badge-xs h-5 w-16 text-gray-400"
                                    >
                                        +{{ $badgeRole['moreCount'] }} more
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="mt-2 flex flex-col gap-2">
                    <h3 class="text-sm">{{ $timeline['timeline']->title }}</h3>
                    <span class="-mt-1 break-all text-xs">
                        {{ $timeline['timeline']->subtitle ?? '' }}
                    </span>
                </div>
            </div>
        @endforeach

        <div class="flex w-full justify-end pt-1">
            <a
                href="{{ route('livewirePageGroup.conference.pages.timelines') }}"
                class="btn btn-primary btn-sm w-16 rounded-md text-xs text-white"
            >
                More
            </a>
        </div>
    @endif
</x-block>
