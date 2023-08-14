<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $livewire?->getTitle() ?? '' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400;1,500;1,600;1,700;1,800&family=Inter:wght@100;200;300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    @vite(['resources/website/css/website.css', 'resources/website/js/website.js'])
</head>

<body class="page page-{{ strtolower($livewire->getTitle()) }} antialiased h-screen" x-data>
    @include('website.layouts.header')
    <div class="mx-auto max-w-7xl p-2 grid grid-cols-12 gap-2">
        <div class="page-leftbar col-span-12 lg:col-span-3 border-2 border-red-700">
            leftbar
        </div>
        <div class="page-content order-first lg:order-none col-span-12 lg:col-span-6 border-2 border-red-700">
            {{ $slot }}
        </div>
        <div class="page-rightbar col-span-12 lg:col-span-3 border-2 border-red-700">
            rightbar
        </div>
    </div>    
    
</body>

</html>
