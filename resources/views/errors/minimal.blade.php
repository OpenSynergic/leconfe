<x-website::layouts.app>
    <div class="text-center">
        <h1 class="mb-4 text-6xl font-semibold text-red-500">
            @yield('code')
        </h1>
        <p class="mb-4 text-lg text-gray-600">@yield('message')</p>
    </div>
</x-website::layouts.app>
