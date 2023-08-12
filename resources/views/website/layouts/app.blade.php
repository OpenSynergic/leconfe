<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $livewire?->getTitle() ?? '' }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    @vite(['resources/website/css/website.css', 'resources/website/js/website.js'])
</head>

<body class="antialiased h-screen" x-data>
    @include('website.layouts.header')
    {{ $slot }}
    
</body>

</html>
