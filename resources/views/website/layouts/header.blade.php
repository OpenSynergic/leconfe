<div class="navbar-container sticky top-0 z-[500] bg-primary text-white">
    <div class="navbar mx-auto max-w-7xl">
        <div class="navbar-start">
            <a class="btn btn-ghost normal-case text-2xl" href="#">Logos</a>
        </div>
        <x-website::navigation />
        <div class="navbar-end gap-x-4 hidden lg:inline-flex">
            @if (\Filament\Facades\Filament::getDefaultPanel()->auth()->user())
                <a href="{{ route('filament.panel.tenant') }}" class="btn btn-sm btn-primary rounded-full px-4">Dashboard</a>
            @else
                <a href="{{ route('filament.panel.tenant') }}" class="btn btn-sm btn-ghost rounded-full px-4">Register</a>
                <a href="{{ route('filament.panel.tenant') }}" class="btn btn-sm rounded-full px-4">Login</a>
            @endif
        </div>
    </div>
</div>
