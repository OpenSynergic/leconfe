<x-website::layouts.main>
    <div class="p-0 sm:p-5 space-y-10">
        <section id="highlight-conference" class="space-y-4">
            <div class="flex flex-col sm:flex-row flex-wrap space-y-4 sm:space-y-0 gap-4">
                <div class="flex flex-col gap-4 flex-1">
                    <div>
                        <img src="https://placehold.co/960x200" class="w-full" alt="">
                    </div>
                    <div class="inline-flex items-center space-x-2">
                        <h1 class="cf-name text-2xl">{{ $currentConference->name }}</h1>
                        @php
                            $badgeClass = '';
                            switch ($currentConference->type) {
                                case \App\Models\Enums\ConferenceType::Offline:
                                    $badgeClass = 'bg-gray-500';
                                    break;
                                case \App\Models\Enums\ConferenceType::Hybrid:
                                    $badgeClass = 'bg-purple-400';
                                    break;
                                case \App\Models\Enums\ConferenceType::Online:
                                    $badgeClass = 'bg-primary';
                                    break;
                            }
                        @endphp

                        <span
                            class="badge {{ $badgeClass }} text-white rounded-full px-3 text-xs flex items-center justify-center h-8 outline-0 border-none">{{ $currentConference->type }}</span>
                    </div>
                    @if ($currentConference->getMeta('description'))
                        <div class="user-content">
                            {{ $currentConference->getMeta('description') }}
                        </div>
                    @endif
                    @if ($currentConference->series->where('active', true)->first()->description)
                        <div>
                            <h2 class="text-base font-medium">Series Description :</h2>
                            <div class="user-content">
                                {{ $currentConference->series->where('active', true)->first()->description }}
                            </div>
                        </div>
                    @endif
                    @if ($topics->isNotEmpty())
                        <div>
                            <h2 class="text-base font-medium mb-1">Topics :</h2>
                            <div class="flex flex-wrap w-full gap-2">
                                @foreach ($topics as $topic)
                                    <span
                                        class="badge badge-outline text-xs border border-gray-300 h-6 text-secondary">{{ $topic->name }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    <div>
                        <a href="{{ route('filament.conference.resources.submissions.index', ['conference' => $currentConference]) }}"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary focus:outline-none focus:ring-2 focus:ring-sky-400 hover:bg-sky-600 w-fit">
                            <x-heroicon-o-document-arrow-up class="h-5 w-5 mr-2" />
                            Submit Now
                        </a>
                    </div>
                </div>
            </div>
        </section>

        @if ($currentConference->sponsors)
            <section id="conference-partner" class="space-y-4">
                <div class="sponsors space-y-4" x-data="carousel">
                    <h2 class="text-xl text-center">Conference Partner</h2>
                    <div class="sponsors-carousel flex items-center w-full gap-4" x-bind="carousel">
                        <button x-on:click="toLeft"
                            class="hidden bg-gray-400 hover:bg-gray-500 h-10 w-10 rounded-full md:flex items-center justify-center">
                            <x-heroicon-m-chevron-left class="h-6 w-fit text-white" />
                        </button>
                        <ul x-ref="slider"
                            class="flex-1 flex w-full snap-x snap-mandatory overflow-x-scroll gap-3 py-4">
                            @foreach ($currentConference->sponsors as $sponsor)
                                <li @class([
                                    'flex shrink-0 snap-start flex-col items-center justify-center',
                                    'ml-auto' => $loop->first,
                                    'mr-auto' => $loop->last,
                                ])>
                                    <img class="max-h-24 w-fit"
                                        src="{{ $sponsor->getFirstMedia('logo')?->getAvailableUrl(['thumb']) }}"
                                        alt="{{ $sponsor->name }}">
                                </li>
                            @endforeach
                        </ul>
                        <button x-on:click="toRight"
                            class="hidden bg-gray-400 hover:bg-gray-500 h-10 w-10 rounded-full md:flex items-center justify-center">
                            <x-heroicon-m-chevron-right class="h-6 w-fit text-white" />
                        </button>
                    </div>
                </div>
            </section>
        @endif

        <section id="conference-detail-tabs" class="space-y-4">
            <div x-data="{ activeTab: 'information' }" class="bg-white">
                <div class="border border-t-0 border-x-0 border-gray-300 flex space-x-1 sm:space-x-2 overflow-x-auto overflow-y-hidden">
                    <button @click="activeTab = 'information'"
                        :class="{ 'text-primary bg-white': activeTab === 'information', 'bg-gray-100': activeTab !== 'information' }"
                        class="px-4 py-2 text-sm hover:text-primary border border-b-white border-gray-300 text-nowrap">Information</button>
                    <button @click="activeTab = 'participant-info'"
                        :class="{ 'text-primary bg-white': activeTab === 'participant-info', 'bg-gray-100': activeTab !== 'participant-info' }"
                        class="px-4 py-2 text-sm hover:text-primary border border-b-white border-gray-300 text-nowrap">Participant Info</button>

                    @foreach ($additional_information as $info)
                        <button @click="activeTab = '{{ strtolower(str_replace(' ', '_', $info['title'])) }}'"
                            :class="{ 'text-primary bg-white': activeTab === '{{ strtolower(str_replace(' ', '_', $info['title'])) }}', 'bg-gray-100': activeTab !== '{{ strtolower(str_replace(' ', '_', $info['title'])) }}' }"
                            class="px-4 py-2 text-sm hover:text-primary border border-b-white border-gray-300 text-nowrap">{{ $info['title'] }}</button>
                    @endforeach
                </div>
                <div x-show="activeTab === 'information'" class="p-4 border border-t-0 border-gray-300 ">
                    <article id="conference-information" class="flex flex-col gap-2">
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
                                        {{ date(setting('format.date'), strtotime($currentConference->date_start)) }} -
                                        {{ date(setting('format.date'), strtotime($currentConference->date_end)) }}
                                    </td>
                                </tr>
                            @endif
                        </table>
                    </article>
                </div>

                <div x-show="activeTab === 'participant-info'" class="p-4 border border-t-0 border-gray-300 ">
                    <article id="participant-info">
                        <p>Quota</p>
                        <div class="overflow-x-auto py-2">
                            <table class="text-sm text-nowrap">
                                <tr class="py-2">
                                    <td class="pr-10">Paper</td>
                                    <td class="pr-2">:</td>
                                    <td class="pr-2">400 Papers</td>
                                    <td class="pr-2">
                                        <span class="badge badge-primary text-xs h-6 ">355 Accepted</span>
                                    </td>
                                    <td class="pr-2"><span class="badge badge-outline text-xs border h-6 text-primary">45
                                            Available</span></td>
                                </tr>
                                <tr>
                                    <td>Participant</td>
                                    <td>:</td>
                                    <td>60 Seats</td>
                                    <td>
                                        <span class="badge badge-primary text-xs h-6">30 Reserved</span>
                                    </td>
                                    <td><span class="badge badge-outline text-xs border h-6 text-primary">30
                                            Available</span></td>
                                </tr>
                            </table>
                        </div>
                    </article>
                </div>

                @foreach ($additional_information as $info)
                    <div x-show="activeTab === '{{ strtolower(str_replace(' ', '_', $info['title'])) }}'"
                        class="p-4 border border-t-0 border-gray-300 ">
                        <article id="{{ strtolower(str_replace(' ', '_', $info['title'])) }}"
                            class="user-content overflow-x-auto">
                            {!! $info['content'] !!}
                        </article>
                    </div>
                @endforeach
            </div>

        </section>

        @if ($participantPosition->isNotEmpty())
            <section id="conference-speakers" class="flex flex-col space-y-4 gap-y-0">
                <div class="flex items-center">
                    <img src="{{ Vite::asset('resources/assets/images/game-icons_public-speaker.svg') }}"
                        alt="">
                    <h2 class="text-xl font-medium pl-2">Speakers</h2>
                </div>
                <div class="cf-speakers space-y-6">
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

        @if ($acceptedSubmission->isNotEmpty())
            <section id="conference-accepted-papers" class="flex flex-col gap-y-0 space-y-4">
                <div class="flex items-center">
                    <img src="{{ Vite::asset('resources/assets/images/mingcute_paper-line.svg') }}" alt="">
                    <h2 class="text-xl font-medium pl-2">Accepted Paper List</h2>
                </div>
                <div class="flex w-full flex-col gap-y-5">
                    @foreach ($acceptedSubmission as $submission)
                        <div class="flex flex-col sm:flex-row">
                            <div class="w-8 flex-none hidden sm:block">
                                <p class="text-lg font-bold">{{ $loop->index + 1 }}.</p>
                            </div>
                            <div
                                class="flex justify-start px-0 sm:px-4 items-center sm:justify-start sm:items-start mt-4 sm:mt-0 flex-none">
                                <img class="sm:w-32 w-24 h-auto"
                                    src="{{ Vite::asset('resources/assets/images/placeholder-vertical.jpg') }}"
                                    alt="Placeholder Image">
                            </div>
                            <div class=" py-2 flex flex-col">
                                <a href="#"
                                    class="text-md font-medium text-primary mb-2">{{ $submission->getMeta('title') }}</a>
                                <a href="#" class="text-sm underline mb-2">https://doi.org/10.2121/jon.v1i01</a>
                                <div class="flex items-center">
                                    <img src="{{ Vite::asset('resources/assets/images/ic_baseline-people.svg') }}"
                                        alt="People Icon" class="w-5 h-5 mr-2">

                                    <p class="text-sm text-gray-700">
                                        Dr. Ghavin Reynara
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</x-website::layouts.main>
