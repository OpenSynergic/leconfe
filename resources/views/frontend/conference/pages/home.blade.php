<x-website::layouts.main>
    @if($currentSerie)
    <div class="space-y-10">
        <section id="highlight-conference" class="space-y-4">
            <div class="flex flex-col sm:flex-row flex-wrap space-y-4 sm:space-y-0 gap-4">
                <div class="flex flex-col gap-4 flex-1">
                    @if ($currentSerie->hasMedia('cover'))
                        <div class="cf-cover">
                            <img class="w-full"
                                src="{{ $currentSerie->getFirstMedia('cover')->getAvailableUrl(['thumb', 'thumb-xl']) }}"
                                alt="{{ $currentSerie->title }}" />
                        </div>
                    @endif
                    <div class="inline-flex items-center space-x-2">
                        <h1 class="cf-title text-2xl">{{ $currentSerie->title }}</h1>
                        <span
                            @class([
                                'badge badge-sm',
                                'badge-secondary' => $currentSerie->type === \App\Models\Enums\ConferenceType::Offline,
                                'badge-warning' => $currentSerie->type === \App\Models\Enums\ConferenceType::Hybrid,
                                'badge-primary' => $currentSerie->type === \App\Models\Enums\ConferenceType::Online,
                            ])>{{ $currentSerie->type }}</span>
                    </div>
                    @if ($currentSerie->getMeta('about'))
                        <div class="user-content">
                            {{ new Illuminate\Support\HtmlString($currentSerie->getMeta('about')) }}
                        </div>
                    @endif
                    @if ($currentSerie->getMeta('additional_content'))
                        <div class="user-content">
                            {{ new Illuminate\Support\HtmlString($currentSerie->getMeta('additional_content')) }}
                        </div>
                    @endif
                    @if ($topics->isNotEmpty())
                        <div>
                            <h2 class="cf-topics text-base font-medium mb-1">Topics :</h2>
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
                            <x-heroicon-o-document-arrow-up class="h-5 w-5" />
                            Submit Now
                        </a>
                    </div>
                </div>
            </div>
        </section>
        @if ($currentConference->sponsors->isNotEmpty())
            <section id="conference-partner" class="space-y-4">
                <div class="sponsors space-y-4" x-data="carousel">
                    <h2 class="text-xl text-center">Conference Partner</h2>
                    <div class="sponsors-carousel flex items-center w-full gap-4" x-bind="carousel">
                        <button x-on:click="toLeft"
                            role="button"
                            class="hidden bg-gray-400 hover:bg-gray-500 h-10 w-10 rounded-full md:flex items-center justify-center">
                            <x-heroicon-m-chevron-left class="h-6 w-fit text-white" />
                            <span class="sr-only">To Left</span>
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
                            role="button"
                            class="hidden bg-gray-400 hover:bg-gray-500 h-10 w-10 rounded-full md:flex items-center justify-center">
                            <x-heroicon-m-chevron-right class="h-6 w-fit text-white" />
                            <span class="sr-only">To Right</span>
                        </button>
                    </div>
                </div>
            </section>
        @endif
        
        @if($additionalInformations->isNotEmpty())
        <section id="conference-additional-informations" class="space-y-4">
            <div x-data="{ activeTab: '{{ $additionalInformations[0]['slug'] }}' }" class="bg-white">
                <div class="border border-t-0 border-x-0 border-gray-300 flex space-x-1 sm:space-x-2 overflow-x-auto overflow-y-hidden">
                    @foreach ($additionalInformations as $info)
                        <button x-on:click="activeTab = '{{ $info['slug'] }}'"
                            :class="{ 'text-primary bg-white': activeTab === '{{ $info['slug'] }}', 'bg-gray-100': activeTab !== '{{ $info['slug'] }}' }"
                            class="px-4 py-2 text-sm hover:text-primary border border-b-white border-gray-300 text-nowrap">{{ $info['title'] }}</button>
                    @endforeach
                </div>
                @foreach ($additionalInformations as $info)
                    <div 
                        x-show="activeTab === '{{ $info['slug'] }}'"
                        class="p-4 border border-t-0 border-gray-300" 
                        @if(!$loop->first) x-cloak @endif
                        >
                        <div class="user-content overflow-x-auto">
                            {{ new Illuminate\Support\HtmlString($info['content']) }}
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
        @endif

        @if ($currentSerie?->speakers()->exists())
            <section id="conference-speakers" class="flex flex-col space-y-4 gap-y-0">
                <div class="flex items-center">
                    <img src="{{ Vite::asset('resources/assets/images/game-icons_public-speaker.svg') }}"
                        alt="">
                    <h2 class="text-xl font-medium pl-2">Speakers</h2>
                </div>
                <div class="cf-speakers space-y-6">
                    @foreach ($currentSerie->speakerRoles as $role)
                        @if ($role->speakers->isNotEmpty())
                            <div class="space-y-4">
                                <h3 class="text-base">{{ $role->name }}</h3>
                                <div class="cf-speaker-list grid gap-2 sm:grid-cols-2">
                                    @foreach ($role->speakers as $role)
                                        <div class="cf-speaker flex items-center h-full gap-2">
                                            <img class="cf-speaker-img object-cover w-16 h-16 rounded-full aspect-square"
                                                src="{{ $role->getFilamentAvatarUrl() }}"
                                                alt="{{ $role->fullName }}" />
                                            <div class="cf-speaker-information">
                                                <div class="cf-speaker-name text-sm text-gray-900">
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
    @else 
    <div>
        <p>Currently no active serie, please create a serie first.</p>
    </div>
    @endif
</x-website::layouts.main>
