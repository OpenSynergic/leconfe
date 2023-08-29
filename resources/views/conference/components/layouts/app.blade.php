<x-conference::layouts.base :livewire="$livewire">
    <div class="flex flex-col h-full gap-2 min-h-screen">
        
        {{-- Load Header Layout --}}
        <x-conference::layouts.header />

        {{-- Load Main Layout --}}
        {{ $slot }}
        
        {{-- Load Footer Layout --}}
        <x-conference::layouts.footer />
    </div>    
</x-conference::layouts.base>