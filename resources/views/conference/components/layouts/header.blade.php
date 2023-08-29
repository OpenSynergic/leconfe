<div class="navbar-container sticky top-0 z-[500] bg-primary text-white">
    <div class="navbar mx-auto max-w-7xl">
        <div class="navbar-start">
            <x-conference::logo/>
        </div>
        <x-conference::navigation :items="$currentConference->getNavigationItems('primary-navigation-menu')"/>
        <div class="navbar-end gap-x-4 hidden lg:inline-flex">
            @if (\Filament\Facades\Filament::getDefaultPanel()->auth()->user())
                <x-conference::link :href="route('filament.panel.tenant')" :spa="false" class="btn btn-sm btn-primary rounded-full px-4">Dashboard</x-conference::link>
            @else
                <x-conference::link :href="route('filament.panel.tenant')" :spa="false" class="btn btn-sm btn-ghost rounded-full px-4">Register</x-conference::link>
                <x-conference::link :href="route('filament.panel.tenant')" :spa="false" class="btn btn-sm rounded-full px-4">Login</x-conference::link>
            @endif
        </div>
    </div>
</div>
