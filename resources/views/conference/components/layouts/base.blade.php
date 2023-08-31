@props([
    'livewire'
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        {{ filled($title = strip_tags($livewire->getTitle())) ? "{$title} - " : null }}
        {{ isset($currentConference) ? $currentConference->name : config('app.name') }}
    </title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400;1,500;1,600;1,700;1,800&family=Inter:wght@100;200;300;400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    @vite(['resources/conference/css/conference.css', 'resources/conference/js/conference.js'])
</head>

<body class="page page-{{ strtolower($livewire->getTitle()) }} antialiased" x-data>
    {{ $slot }}
</body>

</html>
