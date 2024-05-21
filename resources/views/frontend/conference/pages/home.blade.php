<x-website::layouts.main>
    <div class="space-y-10">
        <section id="highlight-conference" class="space-y-4">
            <div class="flex flex-col flex-wrap gap-4 space-y-4 sm:flex-row sm:space-y-0">
                <div class="flex flex-col flex-1 gap-4">
                    @if ($currentConference->hasMedia('cover'))
                        <div class="cf-cover">
                            <img class="w-full"
                                src="{{ $currentConference->getFirstMedia('cover')->getAvailableUrl(['thumb', 'thumb-xl']) }}"
                                alt="{{ $currentConference->name }}" />
                        </div>
                    @endif
                    <div class="inline-flex items-center space-x-2">
                        <h1 class="text-2xl cf-name">{{ $currentConference->name }}</h1>
                        <span
                            @class([
                                'badge badge-sm',
                                'badge-secondary' => $currentConference->type === \App\Models\Enums\ConferenceType::Offline,
                                'badge-warning' => $currentConference->type === \App\Models\Enums\ConferenceType::Hybrid,
                                'badge-primary' => $currentConference->type === \App\Models\Enums\ConferenceType::Online,
                            ])>{{ $currentConference->type }}</span>
                    </div>
                    @if ($currentConference->getMeta('description'))
                        <div class="user-content">
                            {{ $currentConference->getMeta('description') }}
                        </div>
                    @endif
                    @if ($currentSerie)
                        <div class="cf-current-serie">
                            <h2 class="text-base font-medium">Series Description :</h2>
                            <div class="user-content">
                                {{ $currentSerie->description }}
                            </div>
                        </div>
                    @endif
                    @if ($topics->isNotEmpty())
                        <div>
                            <h2 class="mb-1 text-base font-medium cf-topics">Topics :</h2>
                            <div class="flex flex-wrap w-full gap-2">
                                @foreach ($topics as $topic)
                                    <span
                                        class="badge badge-outline badge-sm">{{ $topic->name }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    <div>
                        <a href="{{ route('filament.conference.resources.submissions.index') }}"
                            class="btn btn-primary btn-sm">
                            <x-heroicon-o-document-arrow-up class="w-5 h-5" />
                            Submit Now
                        </a>
                    </div>
                </div>
            </div>
        </section>
        @if ($currentConference->sponsors->isNotEmpty())
            <section id="conference-partner" class="space-y-4">
                <div class="space-y-4 sponsors" x-data="carousel">
                    <h2 class="text-xl text-center">Conference Partner</h2>
                    <div class="flex items-center w-full gap-4 sponsors-carousel" x-bind="carousel">
                        <button x-on:click="toLeft"
                            class="items-center justify-center hidden w-10 h-10 bg-gray-400 rounded-full hover:bg-gray-500 md:flex">
                            <x-heroicon-m-chevron-left class="h-6 text-white w-fit" />
                        </button>
                        <ul x-ref="slider"
                            class="flex flex-1 w-full gap-3 py-4 overflow-x-scroll snap-x snap-mandatory">
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
                            class="items-center justify-center hidden w-10 h-10 bg-gray-400 rounded-full hover:bg-gray-500 md:flex">
                            <x-heroicon-m-chevron-right class="h-6 text-white w-fit" />
                        </button>
                    </div>
                </div>
            </section>
        @endif

        <section id="conference-detail-tabs" class="space-y-4">
            <div x-data="{ activeTab: 'information' }" class="bg-white">
                <div class="flex space-x-1 overflow-x-auto overflow-y-hidden border border-t-0 border-gray-300 border-x-0 sm:space-x-2">
                    <button x-on:click="activeTab = 'information'"
                        :class="{ 'text-primary bg-white': activeTab === 'information', 'bg-gray-100': activeTab !== 'information' }"
                        class="px-4 py-2 text-sm border border-gray-300 hover:text-primary border-b-white text-nowrap">Information</button>
                    <button x-on:click="activeTab = 'participant-info'"
                        :class="{ 'text-primary bg-white': activeTab === 'participant-info', 'bg-gray-100': activeTab !== 'participant-info' }"
                        class="px-4 py-2 text-sm border border-gray-300 hover:text-primary border-b-white text-nowrap">Participant Info</button>

                    @foreach ($additionalInformations as $info)
                        <button x-on:click="activeTab = '{{ strtolower(str_replace(' ', '_', $info['title'])) }}'"
                            :class="{ 'text-primary bg-white': activeTab === '{{ strtolower(str_replace(' ', '_', $info['title'])) }}', 'bg-gray-100': activeTab !== '{{ strtolower(str_replace(' ', '_', $info['title'])) }}' }"
                            class="px-4 py-2 text-sm border border-gray-300 hover:text-primary border-b-white text-nowrap">{{ $info['title'] }}</button>
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
                                        {{ date(Settings::get('format.date'), strtotime($currentConference->date_start)) }} -
                                        {{ date(Settings::get('format.date'), strtotime($currentConference->date_end)) }}
                                    </td>
                                </tr>
                            @endif
                        </table>
                    </article>
                </div>

                <div x-show="activeTab === 'participant-info'" class="p-4 border border-t-0 border-gray-300 " x-cloak>
                    <article id="participant-info">
                        <p>Quota</p>
                        <div class="py-2 overflow-x-auto">
                            <table class="text-sm text-nowrap">
                                <tr class="py-2">
                                    <td class="pr-10">Paper</td>
                                    <td class="pr-2">:</td>
                                    <td class="pr-2">400 Papers</td>
                                    <td class="pr-2">
                                        <span class="h-6 text-xs badge badge-primary ">355 Accepted</span>
                                    </td>
                                    <td class="pr-2"><span class="h-6 text-xs border badge badge-outline text-primary">45
                                            Available</span></td>
                                </tr>
                                <tr>
                                    <td>Participant</td>
                                    <td>:</td>
                                    <td>60 Seats</td>
                                    <td>
                                        <span class="h-6 text-xs badge badge-primary">30 Reserved</span>
                                    </td>
                                    <td><span class="h-6 text-xs border badge badge-outline text-primary">30
                                            Available</span></td>
                                </tr>
                            </table>
                        </div>
                    </article>
                </div>

                @foreach ($additionalInformations as $info)
                    <div x-show="activeTab === '{{ strtolower(str_replace(' ', '_', $info['title'])) }}'"
                        class="p-4 border border-t-0 border-gray-300 " x-cloak>
                        <article id="{{ strtolower(str_replace(' ', '_', $info['title'])) }}"
                            class="overflow-x-auto user-content">
                            {!! $info['content'] !!}
                        </article>
                    </div>
                @endforeach
            </div>
        </section>

        @if ($currentSerie?->speakers()->exists())
            <section id="conference-speakers" class="flex flex-col space-y-4 gap-y-0">
                <div class="flex items-center">
                    <img src="{{ Vite::asset('resources/assets/images/game-icons_public-speaker.svg') }}"
                        alt="">
                    <h2 class="pl-2 text-xl font-medium">Speakers</h2>
                </div>
                <div class="space-y-6 cf-speakers">
                    @foreach ($currentSerie->speakerRoles as $role)
                        @if ($role->speakers->isNotEmpty())
                            <div class="space-y-4">
                                <h3 class="text-base">{{ $role->name }}</h3>
                                <div class="grid gap-2 cf-speaker-list sm:grid-cols-2">
                                    @foreach ($role->speakers as $role)
                                        <div class="flex items-center h-full gap-2 cf-speaker">
                                            <img class="object-cover w-16 h-16 rounded-full cf-speaker-img aspect-square"
                                                src="{{ $role->getFilamentAvatarUrl() }}"
                                                alt="{{ $role->fullName }}" />
                                            <div class="cf-speaker-information">
                                                <div class="text-sm text-gray-900 cf-speaker-name">
                                                    {{ $role->fullName }}
                                                </div>
                                                @if ($role->getMeta('expertise'))
                                                    <div class="cf-speaker-expertise text-2xs text-primary">
                                                        {{ implode(', ', $role->getMeta('expertise') ?? []) }}
                                                    </div>
                                                @endif
                                                @if ($role->getMeta('affiliation'))
                                                    <div class="cf-speaker-affiliation text-2xs text-secondary">
                                                        {{ $role->getMeta('affiliation') }}</div>
                                                @endif
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

        @if($currentProceeding)
            <section id="current-proceeding">
                <div class="mb-6">
                    <x-conference::proceeding :proceeding="$currentProceeding" :title="'Current Proceeding'" />
                </div>
            </section>
        @endif

        @if ($acceptedSubmission->isNotEmpty())
            <section id="conference-accepted-papers" class="flex flex-col space-y-4 gap-y-0">
                <div class="flex items-center">
                    <img src="{{ Vite::asset('resources/assets/images/mingcute_paper-line.svg') }}" alt="">
                    <h2 class="pl-2 text-xl font-medium">Accepted Paper List</h2>
                </div>
                <div class="flex flex-col w-full gap-y-5">
                    @foreach ($acceptedSubmission as $submission)
                        <div class="flex flex-col sm:flex-row">
                            <div class="flex-none hidden w-8 sm:block">
                                <p class="text-lg font-bold">{{ $loop->index + 1 }}.</p>
                            </div>
                            <div
                                class="flex items-center justify-start flex-none px-0 mt-4 sm:px-4 sm:justify-start sm:items-start sm:mt-0">
                                <img class="w-24 h-auto sm:w-32"
                                    src="{{ Vite::asset('resources/assets/images/placeholder-vertical.jpg') }}"
                                    alt="Placeholder Image">
                            </div>
                            <div class="flex flex-col py-2 ">
                                <a href="#"
                                    class="mb-2 font-medium text-md text-primary">{{ $submission->getMeta('title') }}</a>
                                <a href="#" class="mb-2 text-sm underline">https://doi.org/10.2121/jon.v1i01</a>
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
