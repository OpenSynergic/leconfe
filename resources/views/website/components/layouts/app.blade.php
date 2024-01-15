<x-website::layouts.base :livewire="$livewire">
    <div class="flex flex-col h-full gap-3 min-h-screen">

        {{-- Load Header Layout --}}
        <x-website::layouts.header />

        {{-- Load Main Layout --}}
        {{ $slot }}

        {{-- Load Footer Layout --}}
        <x-website::layouts.footer />
    </div>
</x-website::layouts.base>
