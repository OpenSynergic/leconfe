<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        @yield('title')
    </title>
    <meta name="application-name" content="Leconfe">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    @livewireStyles
    @vite(['resources/frontend/css/frontend.css', 'resources/frontend/js/frontend.js'])
</head>
    <body>
        <div class="flex flex-col h-screen gap-3 min-h-screen">
            <div class="navbar-container sticky top-0 z-[60] bg-primary text-white shadow">
                <div class="navbar mx-auto max-w-7xl">
                    <div class="navbar-start items-center w-auto sm:w-1/2 gap-2">
                        <x-website::navigation-menu-mobile />
                        <x-website::logo/>
                    </div>
                </div>
            </div>
            <x-website::layouts.error></x-website::layouts.error>
            <x-website::layouts.footer></x-website::layouts.footer>
        </div>
        @livewireScripts
    </body>
</html>
