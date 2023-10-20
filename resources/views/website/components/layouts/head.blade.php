<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{ MetaTag::render() }}

    <title>
        {{ filled($title = strip_tags($livewire->getTitle())) ? "{$title} - " : null }}
        {{ $contextName ?? config('app.name') }}
    </title>

    @if(isset($favicon))
        <link rel="icon" type="image/x-icon" href="{{ $favicon }}">
    @endif
    @isset($styleSheet)
        <link rel="stylesheet" type="text/css" href="{{ $styleSheet }}">
    @endisset
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    @vite(['resources/website/css/website.css', 'resources/website/js/website.js'])
</head>
