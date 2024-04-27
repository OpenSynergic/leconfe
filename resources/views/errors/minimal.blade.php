<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{ MetaTag::render() }}

    <title>
        Not Found
    </title>
    <meta name="application-name" content="Leconfe">
    <meta name=generator content="Leconfe {{ app()->getCodeVersion() }}">
    @if(isset($favicon))
        <link rel="icon" type="image/x-icon" href="{{ $favicon }}">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    @livewireStyles
    @vite(['resources/frontend/css/frontend.css', 'resources/frontend/js/frontend.js'])

    @isset($styleSheet)
        <link rel="stylesheet" type="text/css" href="{{ $styleSheet }}">
    @endisset

    @if (isset($appearanceColor))
        <style>
            {!! $appearanceColor !!}
        </style>
    @endif
</head>
    <body class="antialiased">
        <div class="flex flex-col h-full gap-3 min-h-screen">
            <div class="navbar-container sticky top-0 z-[60] bg-primary text-white shadow">
                <div class="navbar mx-auto max-w-7xl">
                    <div class="navbar-start items-center w-auto sm:w-1/2 gap-2">
                        <x-website::navigation-menu-mobile />
                        <x-website::logo/>
                    </div>
                </div>
            </div>
            <x-website::layouts.main>
                <div class="space-y-5">
                        <div class="description user-content">
                            {{ new Illuminate\Support\HtmlString($site->getMeta('about')) }}
                        </div>
                        <div class="sponsors space-y-4" x-data="carousel">
                            <h2 class="text-xl font-bold">Our Partners</h2>
                            <div class="sponsors-carousel flex items-center w-full gap-4" x-bind="carousel">
                                <button x-on:click="toLeft"
                                    class="hidden bg-gray-400 hover:bg-gray-500 h-10 w-10 rounded-full md:flex items-center justify-center">
                                    <x-heroicon-m-chevron-left class="h-6 w-fit text-white" />
                                </button>
                                <ul x-ref="slider" class="flex-1 flex w-full snap-x snap-mandatory overflow-x-scroll gap-3 py-4">
                                        <li @class([
                                            'flex shrink-0 snap-start flex-col items-center justify-center',
                                        ])>
                                            <img class="max-h-24 w-fit">
                                        </li>
                                </ul>
                                <button x-on:click="toRight"
                                    class="hidden bg-gray-400 hover:bg-gray-500 h-10 w-10 rounded-full md:flex items-center justify-center">
                                    <x-heroicon-m-chevron-right class="h-6 w-fit text-white" />
                                </button>
                            </div>
                        </div>
            
                    <div class="conferences space-y-4" x-data="{tab: 'current'}" x-cloak>
                        <div class="flex items-center justify-center text-sm flex-wrap">
                            <div class="btn-group flex items-center shadow-sm overflow-x-scroll">
                                <button 
                                    :class="{
                                        'bg-primary text-primary-content' : tab === 'current',
                                        'text-primary' : tab !== 'current',
                                    }"
                                    x-on:click="tab = 'current'"
                                    class="w-40 p-2 border border-primary first:rounded-l last:rounded-r">
                                    Current
                                </button>
                                <button 
                                    :class="{
                                        'bg-primary text-primary-content' : tab === 'upcoming',
                                        'text-primary' : tab !== 'upcoming',
                                    }"
                                    x-on:click="tab = 'upcoming'"
                                    class="w-40 p-2 border-y border-primary first:rounded-l last:rounded-r">
                                    Upcoming
                                </button>
                                <button 
                                    :class="{
                                        'bg-primary text-primary-content' : tab === 'allconferences',
                                        'text-primary' : tab !== 'allconferences',
                                    }"
                                    x-on:click="tab = 'allconferences'"
                                    class="w-40 p-2 border border-primary first:rounded-l last:rounded-r text-nowrap">
                                    All Conferences
                                </button>
                                <a 
                                    href="#"  
                                    class="w-40 p-2 border border-l-0 border-primary text-primary first:rounded-l last:rounded-r flex items-center justify-center gap-2">
                                    <x-heroicon-s-magnifying-glass class="h-4 w-4"/>
                                    Search
                                </a>
                            </div>
                        </div>
                        <div class="conference-current space-y-4" x-show="tab === 'current'">
                            <div class="grid sm:grid-cols-2 gap-6">
                            </div>
                        </div>
                        <div class="conference-current space-y-4" x-show="tab === 'upcoming'">
                            <div class="grid sm:grid-cols-2 gap-6">
                            </div>
                        </div>
                        <div class="conference-current space-y-4" x-show="tab === 'allconferences'">
                            <div class="grid sm:grid-cols-2 gap-6">
                            </div>
                        </div>
                    </div>
                </div>
            </x-website::layouts.main>
            <x-website::layouts.footer></x-website::layouts.footer>
        </div>
        @livewireScripts
    </body>
</html>
