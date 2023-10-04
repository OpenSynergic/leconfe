@props([
    'livewire'
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<x-website::layouts.head :livewire="$livewire"/>

<body class="page page-{{ strtolower($livewire->getTitle()) }} antialiased" x-data>
    {{ $slot }}
</body>

</html>
