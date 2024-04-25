<x-website::layouts.main>
    <div class="space-y-2">
        <section id="highlight-conference" class="p-5 space-y-4">
            <h1 class="text-lg cf-name">{{ $currentConference->name }}</h1>

            <div class="flex flex-col flex-wrap gap-4 space-y-4 sm:flex-row sm:space-y-0">
                @if ($currentConference->hasMedia('thumbnail'))
                    <div class="cf-thumbnail">
                        <img class="w-full rounded max-w-[200px]"
                            src="{{ $currentConference->getFirstMedia('thumbnail')->getAvailableUrl(['thumb', 'thumb-xl']) }}"
                            alt="{{ $currentConference->name }}" />
                    </div>
                @endif
                <div class="flex flex-col flex-1 gap-2">
                    @if ($currentConference->date_start)
                        <div class="inline-flex items-center space-x-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z" />
                            </svg>
                            <time
                                class="text-xs text-secondary">{{ date(Settings::get('format.date'), strtotime($currentConference->date_start)) }}</time>
                        </div>
                    @endif
                    @if ($currentConference->getMeta('description'))
                        <div class="user-content">
                            {{ $currentConference->getMeta('description') }}
                        </div>
                    @endif
                    @if ($topics->isNotEmpty())
                        <div class="flex flex-wrap w-full gap-2">
                            @foreach ($topics as $topic)
                                <span
                                    class="h-6 text-xs border border-gray-300 badge badge-outline text-secondary">{{ $topic->name }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </section>

        @if ($currentConference->date_start || $currentConference->hasMeta('location'))
            <section id="conference-information" class="flex flex-col gap-2 p-5">
                <h2 class="text-heading">Information</h2>
                <table class="w-full text-sm" cellpadding="4">
                    <tr>
                        <td width="80">Type</td>
                        <td width="20">:</td>
                        <td>{{ $currentConference->type }}</td>
                    </tr>
                    @if ($currentConference->hasMeta('location'))
                        <tr>
                            <td>Place</td>
                            <td>:</td>
                            <td>{{ $currentConference->getMeta('location') }}</td>
                        </tr>
                    @endif

                    @if ($currentConference->date_start)
                        <tr>
                            <td>Date</td>
                            <td>:</td>
                            <td>
                                {{ date(Settings::get('format.date'), strtotime($currentConference->date_start)) }} - {{ date(Settings::get('format.date'), strtotime($currentConference->date_end)) }}
                            </td>
                        </tr>
                    @endif
                </table>
            </section>
        @endif

        @if ($participantPosition->isNotEmpty())
            <section id="conference-speakers" class="flex flex-col gap-2 p-5">
                <h2 class="text-heading">Speakers</h2>
                <div class="space-y-6 cf-speakers">
                    @foreach ($participantPosition as $position)
                        @if ($position->speakers->isNotEmpty())
                            <div class="space-y-4">
                                <h3 class="text-base">{{ $position->name }}</h3>
                                <div class="grid gap-2 cf-speaker-list sm:grid-cols-2">
                                    @foreach ($position->speakers as $participant)
                                        <div class="flex h-full gap-2 cf-speaker">
                                            <img class="object-cover w-16 h-16 rounded-full aspect-square"
                                                src="{{ $participant->getFilamentAvatarUrl() }}"
                                                alt="{{ $participant->fullName }}" />
                                            <div>
                                                <div class="text-sm text-gray-900 speaker-name">
                                                    {{ $participant->fullName }}
                                                </div>
                                                <div class="speaker-meta">
                                                    @if ($participant->getMeta('expertise'))
                                                        <div class="speaker-expertise text-2xs text-primary">
                                                            {{ implode(', ', $participant->getMeta('expertise') ?? []) }}
                                                        </div>
                                                    @endif
                                                    @if ($participant->getMeta('affiliation'))
                                                        <div class="speaker-affiliation text-2xs text-secondary">
                                                            {{ $participant->getMeta('affiliation') }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </section>
        @endif


        @if ($currentConference->getMeta('additional_content'))
            <section class="px-5 user-content">
                {!! $currentConference->getMeta('additional_content') !!}
            </section>
        @endif

        @if ($venues->isNotEmpty())
            <section class="p-5 venues">
                <h2 class="text-heading">Venues</h2>
                <div class="space-y-3 venue-list">
                    @foreach ($venues as $venue)                        
                    <div class="flex gap-3 venue">
                        @if ($venue->hasMedia('thumbnail'))
                            <img class="max-w-[100px]" src="{{ $venue->getFirstMedia('thumbnail')->getAvailableUrl(['thumb', 'thumb-xl']) }}">
                        @endif
                        <div class="space-y-2">
                            <div>
                                <a class="relative inline-flex items-center justify-center gap-1 font-thin outline-none group/link">
                                    <span
                                        class="text-base font-semibold group-hover/link:underline group-focus-visible/link:underline">
                                        {{ $venue->name }}
                                    </span>
                                </a>
                                <p class="flex items-center gap-1 text-sm text-gray-500"><x-heroicon-m-map-pin class="size-4" /> {{ $venue->location }}</p>
                            </div>
                            <p class="text-xs text-gray-500">{{ $venue->description }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</x-website::layouts.main>
