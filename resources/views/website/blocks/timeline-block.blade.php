<div class="flex flex-col space-y-1">
    @if (count($timelines) > 0)
        <h2 class="text-heading px-2 mb-1">Informations</h2>
        @foreach ($timelines as $timeline)
            @php
                // initialize
                $timelineBackground = '';
                $timelineMarker = '';

                // Check if it's yesterday
                if ($timeline->date <= now()->subDay()) {
                    // Set background and marker for past timeline
                    $timelineBackground = 'past-timeline';
                    $timelineMarker = 'past-timeline-marker';
                }
                // Check if it's today
                elseif ($timeline->date->isToday()) {
                    // Set background and marker for current timeline
                    $timelineBackground = 'current-timeline';
                    $timelineMarker = 'current-timeline-marker';
                }
                // It's not yesterday or today, so it's upcoming
                else {
                    // Set background and marker for upcoming timeline
                    $timelineBackground = 'upcoming-timeline';
                    $timelineMarker = 'upcoming-timeline-marker';
                }
            @endphp
            <div class="{{ $timelineBackground }}">
                <div class="w-full flex justify-between">
                    <div class="inline-flex items-center gap-2">
                        <div class="{{ $timelineMarker }}"></div>
                        <time class="text-xs">{{ date(setting('format.date'), strtotime($timeline->date)) }}</time>
                    </div>
                    <div class="flex flex-wrap gap-1">
                        @if (count($timeline->roles) > 0)
                            @foreach ($timeline->roles as $role)
                                @php
                                    $badgeRole = '';
                                    $badgeRole = match ($role) {
                                        'Author' => 'author-badge',
                                        'Editor' => 'editor-badge',
                                        'Reviewer' => 'reviewer-badge',
                                        'Participant' => 'participant-badge',
                                        default => '',
                                    };

                                @endphp
                                <div class="inline-flex">
                                    <span class="{{ $badgeRole }}">
                                        {{ $role }}
                                    </span>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
                <div class="flex flex-col gap-2 mt-2">
                    <h5 class="text-sm">{{ $timeline->title }}</h5>
                    <span class="text-xs -mt-1 break-all">{{ $timeline->subtitle ?? '' }}</span>
                </div>
            </div>
        @endforeach
    @endif
</div>
