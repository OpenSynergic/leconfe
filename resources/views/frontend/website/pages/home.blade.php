<x-website::layouts.main class="">
    <div class="space-y-2">
        {{-- <section id="highlight-conference" class="p-5">
            <h1 class="text-heading">Highlight Conference</h1>
            <div @class([
                'space-y-4 sm:space-y-0',
                'sm:grid sm:grid-cols-12 gap-4' => $activeConference->hasMedia('thumbnail'),
            ])>
                @if ($activeConference->hasMedia('thumbnail'))
                    <div class="cf-thumbnail col-span-5">
                        <img class="w-full rounded" src="{{ $activeConference->getFirstMediaUrl('thumbnail', 'thumb') }}"
                            alt="{{ $activeConference->name }}" />
                    </div>
                @endif
                <div @class([
                    'flex flex-col gap-2',
                    'col-span-7' => $activeConference->hasMedia('thumbnail'),
                ])>
                    <h2 class="cf-name text-lg">{{ $activeConference->name }}</h2>
                    @if ($activeConference->date_start)
                        <div class="inline-flex items-center space-x-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z" />
                            </svg>
                            <time
                                class="text-xs text-secondary">{{ date(setting('format.date'), strtotime($activeConference->date_start)) }}</time>
                        </div>
                    @endif
                    @if ($activeConference->getMeta('description'))
                        <p class="text-content">
                            {{ $activeConference->getMeta('description') }}
                        </p>
                    @endif
                    @if ($topics->isNotEmpty())
                        <div class="flex flex-wrap w-full gap-2">
                            @foreach ($topics as $topic)
                                <span
                                    class="badge badge-outline text-xs border border-gray-300 h-6 text-secondary">{{ $topic->name }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </section> --}}
        @if ($upcomingConferences->isNotEmpty())
            <section id="upcoming-conferences" class="p-5">
                <h2 class="text-heading">Upcoming Conferences</h2>
                <div class="grid sm:grid-cols-3 gap-2">
                    @foreach ($upcomingConferences as $conference)
                        <div class="cf-upcoming-conference h-full">
                            <div class="rounded border bg-white p-2">
                                @if ($conference->hasMedia('thumbnail'))
                                    <img class="h-auto w-full object-cover aspect-square rounded-sm"
                                        src="{{ $conference->getFirstMedia('thumbnail')->getAvailableUrl(['thumb']) }}"
                                        alt="{{ $conference->name }}" />
                                @endif
                                <h3 class="text-sm tracking-normal text-gray-900 mb-3 mt-1">
                                    {{ $conference->name }}
                                </h3>
                                @if ($conference->date_start)
                                    <div class="cf-upcomiong-enrollment text-xs">Enrollment start on
                                        {{ date(setting('format.date'), strtotime($conference->date_start)) }}
                                    </div>
                                @endif
                                {{-- <div class="flex justify-end mt-2">
                                    <button
                                        class="btn btn-primary btn-sm btn-outline rounded mt-3 font-normal">Enrol</button>
                                </div> --}}
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</x-website::layouts.main>
