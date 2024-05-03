@props([
    'title' => null,
])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <x-website::layouts.head :title="$title" />

    <body class="page antialiased" x-data>
        {{ $slot }}

        @livewireScriptConfig
    </body>
</html>
