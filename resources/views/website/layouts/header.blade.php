<div class="bg-primary text-white">
    <div class="navbar max-w-7xl mx-auto">
        <div class="navbar-start">
            <a class="btn btn-ghost normal-case text-xl">Logos</a>
        </div>
        <x-website::navigation />
        <div class="navbar-end gap-x-4 hidden lg:inline-flex">
            <button class="btn btn-sm btn-ghost rounded-full px-4">Register</button>
            <button class="btn btn-sm rounded-full px-4">Login</button>
        </div>
    </div>
</div>


{{-- <nav x-data="{
    navigationMenuOpen: false,
    navigationMenu: '',
    navigationMenuCloseDelay: 200,
    navigationMenuCloseTimeout: null,
    navigationMenuLeave() {
        let that = this;
        this.navigationMenuCloseTimeout = setTimeout(() => {
            that.navigationMenuClose();
        }, this.navigationMenuCloseDelay);
    },
    navigationMenuReposition(navElement) {
        this.navigationMenuClearCloseTimeout();
        this.$refs.navigationDropdown.style.left = navElement.offsetLeft + 'px';
        this.$refs.navigationDropdown.style.marginLeft = (navElement.offsetWidth / 2) + 'px';
    },
    navigationMenuClearCloseTimeout() {
        clearTimeout(this.navigationMenuCloseTimeout);
    },
    navigationMenuClose() {
        this.navigationMenuOpen = false;
        this.navigationMenu = '';
    }
}" class="relative z-10 w-auto">
    <div class="relative">
        <ul
            class="flex items-center justify-center flex-1 p-1 space-x-1 list-none border rounded-md text-neutral-700 group border-neutral-200/80">
            <li>
                <button
                    :class="{
                        'bg-neutral-100': navigationMenu == 'getting-started',
                        'hover:bg-neutral-100': navigationMenu !=
                            'getting-started'
                    }"
                    @mouseover="navigationMenuOpen=true; navigationMenuReposition($el); navigationMenu='getting-started'"
                    @mouseleave="navigationMenuLeave()"
                    class="inline-flex items-center justify-center h-10 px-4 py-2 text-sm font-medium transition-colors rounded-md hover:text-neutral-900 focus:outline-none disabled:opacity-50 disabled:pointer-events-none group w-max">
                    <span>Getting started</span>
                    <svg :class="{ '-rotate-180': navigationMenuOpen == true && navigationMenu == 'getting-started' }"
                        class="relative top-[1px] ml-1 h-3 w-3 ease-out duration-300" xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" aria-hidden="true">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>
            </li>
            <li>
                <button
                    :class="{
                        'bg-neutral-100': navigationMenu == 'learn-more',
                        'hover:bg-neutral-100': navigationMenu !=
                            'learn-more'
                    }"
                    @mouseover="navigationMenuOpen=true; navigationMenuReposition($el); navigationMenu='learn-more'"
                    @mouseleave="navigationMenuLeave()"
                    class="inline-flex items-center justify-center h-10 px-4 py-2 text-sm font-medium transition-colors rounded-md hover:text-neutral-900 focus:outline-none disabled:opacity-50 disabled:pointer-events-none bg-background hover:bg-neutral-100 group w-max">
                    <span>Learn More</span>
                    <svg :class="{ '-rotate-180': navigationMenuOpen == true && navigationMenu == 'learn-more' }"
                        class="relative top-[1px] ml-1 h-3 w-3 ease-out duration-300" xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" aria-hidden="true">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>
            </li>
            <li>
                <a href="#_"
                    class="inline-flex items-center justify-center h-10 px-4 py-2 text-sm font-medium transition-colors rounded-md hover:text-neutral-900 focus:outline-none disabled:opacity-50 disabled:pointer-events-none bg-background hover:bg-neutral-100 group w-max">
                    Documentation
                </a>
            </li>
        </ul>
    </div>
    <div x-ref="navigationDropdown" x-show="navigationMenuOpen" x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-90" @mouseover="navigationMenuClearCloseTimeout()"
        @mouseleave="navigationMenuLeave()"
        class="absolute top-0 pt-3 duration-200 ease-out -translate-x-1/2 translate-y-11" x-cloak>

        <div
            class="flex justify-center w-auto h-auto overflow-hidden bg-white border rounded-md shadow-sm border-neutral-200/70">

            <div x-show="navigationMenu == 'getting-started'"
                class="flex items-stretch justify-center w-full max-w-2xl p-6 gap-x-3">
                <div class="flex-shrink-0 w-48 rounded pt-28 pb-7 bg-gradient-to-br from-neutral-800 to-black">
                    <div class="relative px-7 space-y-1.5 text-white">
                        <svg class="block w-auto h-9" viewBox="0 0 180 180" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M67.683 89.217h44.634l30.9 53.218H36.783l30.9-53.218Z" fill="currentColor" />
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M77.478 120.522h21.913v46.956H77.478v-46.956Zm-34.434-29.74 45.59-78.26 46.757 78.26H43.044Z"
                                fill="currentColor" />
                        </svg>
                        <span class="block font-bold">Pines UI</span>
                        <span class="block text-sm opacity-60">An Alpine and Tailwind UI library</span>
                    </div>
                </div>
                <div class="w-72">
                    <a href="#_" @click="navigationMenuClose()"
                        class="block px-3.5 py-3 text-sm rounded hover:bg-neutral-100">
                        <span class="block mb-1 font-medium text-black">Introduction</span>
                        <span class="block font-light leading-5 opacity-50">Re-usable elements built using Alpine JS and
                            Tailwind CSS.</span>
                    </a>
                    <a href="#_" @click="navigationMenuClose()"
                        class="block px-3.5 py-3 text-sm rounded hover:bg-neutral-100">
                        <span class="block mb-1 font-medium text-black">How to use</span>
                        <span class="block leading-5 opacity-50">Couldn't be easier. It's is as simple as copy, paste,
                            and preview.</span>
                    </a>
                    <a href="#_" @click="navigationMenuClose()"
                        class="block px-3.5 py-3 text-sm rounded hover:bg-neutral-100">
                        <span class="block mb-1 font-medium text-black">Contributing</span>
                        <span class="block leading-5 opacity-50">Feel free to contribute your expertise. All these
                            elements are open-source.</span>
                    </a>
                </div>
            </div>
            <div x-show="navigationMenu == 'learn-more'" class="flex items-stretch justify-center w-full p-6">
                <div class="w-72">
                    <a href="#_" @click="navigationMenuClose()"
                        class="block px-3.5 py-3 text-sm rounded hover:bg-neutral-100">
                        <span class="block mb-1 font-medium text-black">Tailwind CSS</span>
                        <span class="block font-light leading-5 opacity-50">A utility first CSS framework for building
                            amazing websites.</span>
                    </a>
                    <a href="#_" @click="navigationMenuClose()"
                        class="block px-3.5 py-3 text-sm rounded hover:bg-neutral-100">
                        <span class="block mb-1 font-medium text-black">Laravel</span>
                        <span class="block font-light leading-5 opacity-50">The perfect all-in-one framework for
                            building amazing apps.</span>
                    </a>
                    <a href="#_" @click="navigationMenuClose()"
                        class="block px-3.5 py-3 text-sm rounded hover:bg-neutral-100">
                        <span class="block mb-1 font-medium text-black">Pines UI</span>
                        <span class="block leading-5 opacity-50">An Alpine JS and Tailwind CSS UI library for awesome
                            people. </span>
                    </a>
                </div>
                <div class="w-72">
                    <a href="#_" @click="navigationMenuClose()"
                        class="block px-3.5 py-3 text-sm rounded hover:bg-neutral-100">
                        <span class="block mb-1 font-medium text-black">ApineJS</span>
                        <span class="block font-light leading-5 opacity-50">A framework without the complex setup or
                            heavy dependencies.</span>
                    </a>
                    <a href="#_" @click="navigationMenuClose()"
                        class="block px-3.5 py-3 text-sm rounded hover:bg-neutral-100">
                        <span class="block mb-1 font-medium text-black">Livewire</span>
                        <span class="block leading-5 opacity-50">A seamless integration of server-side and client-side
                            interactions.</span>
                    </a>
                    <a href="#_" @click="navigationMenuClose()"
                        class="block px-3.5 py-3 text-sm rounded hover:bg-neutral-100">
                        <span class="block mb-1 font-medium text-black">Tails</span>
                        <span class="block leading-5 opacity-50">The ultimate Tailwind CSS design tool that helps you
                            craft beautiful websites.</span>
                    </a>
                </div>
            </div>

        </div>
    </div>
</nav> --}}
