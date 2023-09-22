@php($primaryNavigationItems = $currentConference->getNavigationItems('primary-navigation-menu'))


<div class="navbar-container sticky top-0 z-[60] bg-sky-400 text-white">
    <div class="navbar mx-auto max-w-7xl">
        <div class="navbar-start items-center w-auto sm:w-1/2 gap-2">
            <x-conference::navigation-mobile :items="$primaryNavigationItems"/>
            <x-conference::logo/>
        </div>
        <x-conference::navigation :items="$primaryNavigationItems"/>
        <div class="navbar-end gap-x-4 hidden lg:inline-flex">
            @if (\Filament\Facades\Filament::getDefaultPanel()->auth()->user())
                <x-conference::link :href="route('filament.panel.tenant')" :spa="false" class="btn btn-sm btn-white rounded px-4 font-normal text-gray-900">Dashboard</x-conference::link>
            @else
                <x-conference::link :href="route('livewirePageGroup.website.pages.register')" :spa="false" class="btn btn-sm btn-ghost rounded px-4 font-normal text-white">Register</x-conference::link>
                <x-conference::link :href="route('livewirePageGroup.website.pages.login')" :spa="false" class="btn btn-sm rounded px-4 font-normal text-gray-900">Login</x-conference::link>
            @endif
        </div>
    </div>
</div>
