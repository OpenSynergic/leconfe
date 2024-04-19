@php
    $primaryNavigationItems = app()->getNavigationItems('primary-navigation-menu');
    $userNavigationMenu = app()->getNavigationItems('user-navigation-menu');
@endphp


<div class="navbar-container sticky top-0 z-[60] bg-primary text-white shadow">
    <div class="navbar mx-auto max-w-7xl">
        <div class="navbar-start items-center w-auto sm:w-1/2 gap-2">
            <x-website::navigation-menu-mobile />
            <x-website::logo/>
        </div>
        <div class="navbar-center hidden lg:flex relative z-10 w-auto">
            <x-website::navigation-menu :items="$primaryNavigationItems" />
        </div>
        <div class="navbar-end gap-x-4 hidden lg:inline-flex">
            <x-website::navigation-menu :items="$userNavigationMenu" />
        </div>
    </div>
</div>
