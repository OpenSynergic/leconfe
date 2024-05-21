<x-website::layouts.main>
    <div class="space-y-5">
        @if($site->getMeta('about'))
            <div class="description user-content">
                {{ new Illuminate\Support\HtmlString($site->getMeta('about')) }}
            </div>
        @endif

        <div class="conferences space-y-4" x-data="{tab: 'current'}" x-cloak>
            <div class="flex items-center justify-center text-sm flex-wrap">
                <div class="btn-group flex items-center shadow-sm overflow-x-scroll">
                    <button 
                        :class="{
                            'bg-primary text-primary-content' : tab === 'current',
                            'text-primary' : tab !== 'current',
                        }"
                        x-on:click="tab = 'current'"
                        class="w-40 p-2 border border-primary first:rounded-l last:rounded-r">
                        Current
                    </button>
                    <button 
                        :class="{
                            'bg-primary text-primary-content' : tab === 'upcoming',
                            'text-primary' : tab !== 'upcoming',
                        }"
                        x-on:click="tab = 'upcoming'"
                        class="w-40 p-2 border-y border-primary first:rounded-l last:rounded-r">
                        Upcoming
                    </button>
                    <button 
                        :class="{
                            'bg-primary text-primary-content' : tab === 'allconferences',
                            'text-primary' : tab !== 'allconferences',
                        }"
                        x-on:click="tab = 'allconferences'"
                        class="w-40 p-2 border border-primary first:rounded-l last:rounded-r text-nowrap">
                        All Conferences
                    </button>
                    <a 
                        href="{{ route('livewirePageGroup.website.pages.search') }}"  
                        class="w-40 p-2 border border-l-0 border-primary text-primary first:rounded-l last:rounded-r flex items-center justify-center gap-2">
                        <x-heroicon-s-magnifying-glass class="h-4 w-4"/>
                        Search
                    </a>
                </div>
            </div>
            <div class="conference-current space-y-4" x-show="tab === 'current'">
                @if($currentConferences->isNotEmpty())
                    <div class="grid sm:grid-cols-2 gap-6">
                        @foreach ($currentConferences as $conference)
                            <x-website::conference-summary :conference="$conference" />
                        @endforeach
                    </div>
                    @if($currentConferences->hasPages())
                        {{ $currentConferences->links('livewire.simple-pagination') }}
                    @endif
                @else
                <div class="text-center my-12">
                    <p class="text-lg font-bold">There are no conferences taking place at this time</p>
                </div>
                @endif
            </div>
            <div class="conference-upcoming space-y-4" x-show="tab === 'upcoming'">
                @if($upcomingConferences->isNotEmpty())
                    <div class="grid sm:grid-cols-2 gap-6">
                        @foreach ($upcomingConferences as $conference)
                            <x-website::conference-summary :conference="$conference" />
                        @endforeach
                    </div>
                    @if($upcomingConferences->hasPages())
                        {{ $upcomingConferences->links('livewire.simple-pagination') }}
                    @endif
                @else
                    <div class="text-center my-12">
                        <p class="text-lg font-bold">There are no upcoming conferences</p>
                    </div>
                @endif
            </div>
            <div class="conference-all space-y-4" x-show="tab === 'allconferences'">
                @if($allConferences->isNotEmpty())
                    <div class="grid sm:grid-cols-2 gap-6">
                        @foreach ($allConferences as $conference)
                            <x-website::conference-summary :conference="$conference" />
                        @endforeach
                    </div>
                    @if($allConferences->hasPages())
                        {{ $allConferences->links('livewire.simple-pagination') }}
                    @endif
                @else
                    <div class="text-center my-12">
                        <p class="text-lg font-bold">There are no conferences</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-website::layouts.main>
