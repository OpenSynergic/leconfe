<x-website::layouts.main>
    <div class="space-y-5">
        @if($site->getMeta('about'))
            <div class="description user-content">
                {{ new Illuminate\Support\HtmlString($site->getMeta('about')) }}
            </div>
        @endif
        @if(!$sponsors->isEmpty())
            <div class="sponsors space-y-4" x-data="carousel">
                <h2 class="text-xl font-bold">Our Partners</h2>
                <div class="sponsors-carousel flex items-center w-full gap-4" x-bind="carousel">
                    <button x-on:click="toLeft"
                        class="hidden bg-gray-400 hover:bg-gray-500 h-10 w-10 rounded-full md:flex items-center justify-center">
                        <x-heroicon-m-chevron-left class="h-6 w-fit text-white" />
                    </button>
                    <ul x-ref="slider" class="flex-1 flex w-full snap-x snap-mandatory overflow-x-scroll gap-3 py-4">
                        @foreach ($sponsors as $sponsor)
                            <li @class([
                                'flex shrink-0 snap-start flex-col items-center justify-center',
                                'ml-auto' => $loop->first,
                                'mr-auto' => $loop->last,
                            ])>
                                <img class="max-h-24 w-fit" src="{{ $sponsor->getFirstMedia('logo')?->getAvailableUrl(['thumb']) }}"
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
        @endif

        <div class="conferences space-y-4" x-data="{tab: 'current'}" x-cloak>
            <div class="flex items-center justify-center text-sm flex-wrap">
                <div class="btn-group flex items-center shadow-sm rounded-lg">
                    <button 
                        :class="{
                            'bg-primary text-primary-content' : tab === 'current',
                            'bg-white text-primary' : tab !== 'current',
                        }"
                        x-on:click="tab = 'current'"
                        class="w-40 py-2 border border-primary first:rounded-l-lg last:rounded-r-lg">
                        Current
                    </button>
                    <button 
                        :class="{
                            'bg-primary text-white' : tab === 'upcoming',
                            'bg-white text-primary' : tab !== 'upcoming',
                        }"
                        x-on:click="tab = 'upcoming'"
                        class="w-40 py-2 border-y border-primary first:rounded-l-lg last:rounded-r-lg">
                        Upcoming
                    </button>
                    <button 
                        :class="{
                            'bg-primary text-white' : tab === 'allconferences',
                            'bg-white text-primary' : tab !== 'allconferences',
                        }"
                        x-on:click="tab = 'allconferences'"
                        class="w-40 py-2 border border-primary first:rounded-l-lg last:rounded-r-lg">
                        All Conferences
                    </button>
                    <a 
                        href="#"  
                        class="w-40 py-2 border border-l-0 border-primary text-primary first:rounded-l-lg last:rounded-r-lg flex items-center justify-center gap-2">
                        <x-heroicon-s-magnifying-glass class="h-4 w-4"/>
                        Search
                    </a>
                </div>
            </div>
            <div class="conference-current sm:grid sm:grid-cols-2 gap-4" x-show="tab === 'current'">
                @foreach ($currentConferences as $conference)
                   <x-website::conference-summary :conference="$conference" />
                @endforeach
            </div>
            <div class="conference-current sm:grid sm:grid-cols-2 gap-4" x-show="tab === 'upcoming'">
                @foreach ($upcomingConferences as $conference)
                   <x-website::conference-summary :conference="$conference" />
                @endforeach
            </div>
            <div class="conference-current sm:grid sm:grid-cols-2 gap-4" x-show="tab === 'allconferences'">
                @foreach ($allConferences as $conference)
                   <x-website::conference-summary :conference="$conference" />
                @endforeach
            </div>
        </div>
        {{-- <div class="conferences space-y-4" x-data="{tab: 'current'}">
            <div class="button-group flex items-center justify-center text-sm flex-wrap">
                <button class="w-40 py-2 bg-primary border border-primary text-white first:rounded-l-lg last:rounded-r-lg">
                    Current
                </button>
                <button class="w-40 py-2 bg-white border-y border-primary text-primary first:rounded-l-lg last:rounded-r-lg">
                    Upcoming
                </button>
                <button class="w-40 py-2 bg-white border border-primary text-primary first:rounded-l-lg last:rounded-r-lg">
                    All Conferences
                </button>
                <button class="w-40 py-2 bg-white border border-l-0 border-primary text-primary first:rounded-l-lg last:rounded-r-lg">
                    Subject
                </button>
            </div>
            <div class="conference-list sm:grid sm:grid-cols-2 gap-4">
                @foreach ($currentConferences as $conference)
                   <x-website::conference-summary :conference="$conference" />
                @endforeach
            </div>
        </div> --}}
    </div>
</x-website::layouts.main>
