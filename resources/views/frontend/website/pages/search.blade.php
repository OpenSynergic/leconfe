<x-website::layouts.main>
    <div class="space-y-4 grid">
        <div class="grid justify-items-center">
            <h1 class="text-xl font-medium">Search Conference</h1>
        </div>
        <form class="max-w-xl w-full mx-auto space-y-2">
            <div class="flex">
                <div class="relative w-full block">
                    <input 
                        wire:model.live.debounce.500ms="query"
                        type="search"  
                        placeholder="Search ..." 
                        class="block p-2.5 w-full z-20 text-sm text-gray-900 bg-gray-50 rounded border border-gray-300  focus:border-primary" 
                        required 
                    />
                    <button type="submit" class="absolute top-0 end-0 px-4 flex items-center gap-2 text-sm font-medium h-full text-white bg-primary rounded-e border border-primary">
                        <span class="">Search</span>
                        <x-heroicon-s-magnifying-glass class="h-4 w-4"/>
                    </button>
                </div>
            </div>
            <div class="buttons flex items-center justify-end">
                <button type="button" class="text-sm flex items-center gap-2 text-primary">Advanced Search <x-heroicon-c-chevron-down class="h-4 w-4"/></button>
            </div>
            <div>
                <div class="mb-6">
                    <select id="countries" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        <option selected>Topics</option>
                    </select>
                </div> 
            </div>
        </form>

        @if($searchResults->isNotEmpty())
        <div id="search-results" class="search-results grid sm:grid-cols-2 gap-4">
            @foreach ($searchResults as $conference)
                <x-website::conference-summary :conference="$conference" />
            @endforeach 
        </div>
        <div>
            {{ $searchResults->links('livewire.simple-pagination') }}
        </div>
        @else 
        <div class="flex items-center justify-center w-full">
            <p class="text-gray-500">No conference found</p>
        </div>
        @endif

    </div>
</x-website::layouts.main>