<x-website::layouts.app title="{{ $message }}">
    <x-website::layouts.main>
        <div class="text-center">
            <h1 class="mb-4 text-6xl font-semibold text-red-500">
                @yield('code')
            </h1>
            <p class="mb-4 text-lg text-gray-600">{{ $message }}</p>
            <x-website::link class="btn btn-primary btn-sm" :href="$homeUrl">
                Home
            </x-website::link>
        </div>
    </x-website::layouts.main>
</x-website::layouts.app>
