<nav class="navbar-center hidden lg:flex relative z-10 w-auto" x-navigation>
    <div class="relative">
        <ul class="flex items-center justify-center flex-1 p-1 space-x-1 list-none group">
            <li>
                <x-website::navigation.dropdown-trigger key="getting-started">
                    <span>Getting Started</span>
                </x-website::navigation.dropdown-trigger>
            </li>
            <li>
                <x-website::navigation.dropdown-trigger key="getting-started">
                    <span>Test</span>
                </x-website::navigation.dropdown-trigger>
            </li>
        </ul>
    </div>
    <div x-navigation:dropdown class="absolute top-0 duration-200 ease-out -translate-x-1/2 translate-y-11"
        x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-90"
        x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90" x-cloak>
        <x-website::navigation.dropdown-content key="getting-started">
            <a href="{{ route('filament.panel.tenant') }}"
                class="relative flex hover:bg-neutral-100 items-center rounded px-2 py-1.5 text-sm outline-none transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" class="w-4 h-4 mr-2">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                <span>Team</span>
            </a>
        </x-website::navigation.dropdown-content>
    </div>
</nav>