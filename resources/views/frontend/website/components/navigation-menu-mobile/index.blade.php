@php
    $primaryNavigationItems = app()->getNavigationItems('primary-navigation-menu');
    $userNavigationItems = app()->getNavigationItems('user-navigation-menu');
@endphp

<aside class="flex items-center lg:hidden" x-slide-over>
    <button @@click="toggleSlideOver" class="btn btn-square btn-sm btn-ghost">
        <x-heroicon-o-bars-3 x-show="!slideOverOpen" x-cloak />
        <x-heroicon-o-x-mark x-show="slideOverOpen" x-cloak />
    </button>
    <template x-teleport="body">
        <div x-show="slideOverOpen" @@keydown.window.escape="closeSlideOver" class="relative z-50">
            <div x-show="slideOverOpen" x-transition.opacity.duration.600ms @@click="closeSlideOver"
                class="fixed inset-0 backdrop-blur-[2px]"></div>
            <div class="fixed inset-0 overflow-hidden">
                <div class="absolute inset-0 overflow-hidden">
                    <div class="fixed inset-y-0 flex max-w-full pr-10">
                        <div x-show="slideOverOpen" @@click.away="closeSlideOver"
                            x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700"
                            x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
                            x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700"
                            x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full"
                            class="w-screen max-w-xs">
                            <div
                                class="flex flex-col h-full pt-[5rem] overflow-y-scroll bg-white border-r shadow-lg border-neutral-100/70 justify-between">
                                <div class="primary-navigations-menu-mobile">
                                    <ul role="list space-y-2">
                                        @foreach ($primaryNavigationItems as $item)
                                            @if(!$item->isDisplayed())
                                                @continue
                                            @endif
                                            @if ($item->children->isEmpty())
                                                <li class="navigation-menu-item relative">
                                                    <x-website::link @class([
                                                        'hover:bg-base-content/10 items-center py-2 px-4 pr-6 text-sm outline-none transition-colors gap-4 w-full flex',
                                                        'text-primary font-semibold' => request()->url() === $item->getUrl(),
                                                        'text-slate-900 font-medium' => request()->url() !== $item->getUrl(),
                                                    ]) :href="$item->getUrl()">
                                                        {{ $item->getLabel() }}
                                                    </x-website::link>
                                                </li>
                                            @else
                                                <li x-data="{ open: false }" class="navigation-menu-item relative">
                                                    <button 
                                                        x-ref="button"
                                                        @@click="open = !open"
                                                        class="hover:bg-base-content/10 py-2 px-4 pr-6 text-sm outline-none transition-colors gap-4 w-full flex items-center justify-between text-slate-900 font-medium"
                                                        >
                                                        <span>{{ $item->getLabel() }}</span>
                                                        <svg :class="{ '-rotate-180': open}"
                                                            class="transition relative top-[1px] ml-1 h-3 w-3" xmlns="http://www.w3.org/2000/svg"
                                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round" aria-hidden="true">
                                                            <polyline points="6 9 12 15 18 9"></polyline>
                                                        </svg>
                                                    </button>
                                                    <ul x-show="open" x-collapse class="mt-1">
                                                        @foreach ($item->children as $key => $childItem)
                                                            <li class="navigation-menu-item relative">
                                                                <x-website::link @class([
                                                                    'hover:bg-base-content/10 items-center py-2 px-4 pr-6 text-sm outline-none transition-colors gap-4 w-full flex',
                                                                    'text-primary font-semibold' => request()->url() === $item->getUrl(),
                                                                    'text-slate-900 font-medium' => request()->url() !== $item->getUrl(),
                                                                ]) :href="$item->getUrl()">
                                                                    {{ $childItem->getLabel() }}
                                                                </x-website::link>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="user-navigations-menu-mobile">
                                    <ul role="list space-y-2">
                                       @foreach ($userNavigationItems as $item)
                                            @if(!$item->isDisplayed())
                                                @continue
                                            @endif
                                            @if ($item->children->isEmpty())
                                                <li class="navigation-menu-item relative">
                                                    <x-website::link @class([
                                                        'hover:bg-base-content/10 items-center py-2 px-4 pr-6 text-sm outline-none transition-colors gap-4 w-full flex',
                                                        'text-primary font-semibold' => request()->url() === $item->getUrl(),
                                                        'text-slate-900 font-medium' => request()->url() !== $item->getUrl(),
                                                    ]) :href="$item->getUrl()">
                                                        {{ $item->getLabel() }}
                                                    </x-website::link>
                                                </li>
                                            @else
                                                <li x-data="{ open: false }" class="navigation-menu-item relative">
                                                    <button 
                                                        x-ref="button"
                                                        @@click="open = !open"
                                                        class="hover:bg-base-content/10 py-2 px-4 pr-6 text-sm outline-none transition-colors gap-4 w-full flex items-center justify-between text-slate-900 font-medium"
                                                        >
                                                        <span>{{ $item->getLabel() }}</span>
                                                        <svg :class="{ '-rotate-180': open}"
                                                            class="transition relative top-[1px] ml-1 h-3 w-3" xmlns="http://www.w3.org/2000/svg"
                                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round" aria-hidden="true">
                                                            <polyline points="6 9 12 15 18 9"></polyline>
                                                        </svg>
                                                    </button>
                                                    <ul x-show="open" x-collapse class="mt-1">
                                                        @foreach ($item->children as $key => $childItem)
                                                            <li class="navigation-menu-item relative">
                                                                <x-website::link @class([
                                                                    'hover:bg-base-content/10 items-center py-2 px-4 pr-6 text-sm outline-none transition-colors gap-4 w-full flex',
                                                                    'text-primary font-semibold' => request()->url() === $item->getUrl(),
                                                                    'text-slate-900 font-medium' => request()->url() !== $item->getUrl(),
                                                                ]) :href="$item->getUrl()">
                                                                    {{ $childItem->getLabel() }}
                                                                </x-website::link>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                                {{-- <x-website::navigation-mobile.items :items="$items" />
                                <div class="flex flex-col gap-2">
                                    @if (\Filament\Facades\Filament::getDefaultPanel()->auth()->user())
                                        <x-website::link :href="route('filament.panel.tenant')" :spa="false"
                                            class="btn btn-sm btn-primary rounded px-4 font-normal">Dashboard</x-website::link>
                                    @else
                                        <x-website::link :href="route('livewirePageGroup.website.pages.register')" :spa="false"
                                            class="btn btn-sm btn-primary rounded px-4 font-normal">Register</x-website::link>
                                        <x-website::link :href="route('livewirePageGroup.website.pages.login')" :spa="false"
                                            class="btn btn-sm rounded px-4 font-normal text-gray-900">Login</x-website::link>
                                    @endif
                                </div> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
</aside>
