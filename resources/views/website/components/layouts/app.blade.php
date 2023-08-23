<x-website::layouts.base>
    <div class="flex flex-col h-full gap-2 min-h-screen">
        
        {{-- Load Header Layout --}}
        <x-website::layouts.header />

        {{-- Load Main Layout --}}
        <x-website::layouts.main />
        
        {{-- Load Footer Layout --}}
        <x-website::layouts.footer />
    </div>    
</x-website::layouts.base>