@props([
    'title' => null,
])

<head>
    <title>
        {{ $title ? strip_tags($title) . ' - ' : null }}
        {{ $contextName ?? config('app.name') }}
    </title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="application-name" content="Leconfe" />
    <meta name="generator" content="Leconfe {{ app()->getCodeVersion() }}" />

    {{ MetaTag::render() }}

    @if (isset($favicon))
        <link rel="icon" type="image/x-icon" href="{{ $favicon }}" />
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet"
    />
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    @livewireStyles
    @vite(['resources/frontend/css/frontend.css', 'resources/frontend/js/frontend.js'])

    @isset($styleSheet)
        <link rel="stylesheet" type="text/css" href="{{ $styleSheet }}" />
    @endisset

    @if (isset($appearanceColor))
        <style>
            {!! $appearanceColor !!}
        </style>
    @endif
</head>
