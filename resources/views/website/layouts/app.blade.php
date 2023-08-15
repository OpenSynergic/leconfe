<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $livewire?->getTitle() ?? '' }}</title>
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
    @vite(['resources/website/css/website.css', 'resources/website/js/website.js'])
</head>

<body class="page page-{{ strtolower($livewire->getTitle()) }} antialiased" x-data>
    <div class="flex flex-col h-full gap-2 min-h-screen">
        @include('website.layouts.header')
        <div class="mx-auto max-w-7xl flex flex-col lg:grid grid-cols-12 gap-2 grow w-full">
            <div class="page-leftbar">
                <div class="card card-compact bg-white border">
                    <div class="card-body">
                        <h2 class="card-title">Shoes!</h2>
                        <p>If a dog chews shoes whose shoes does he choose?</p>
                        <div class="card-actions justify-end">
                            <button class="btn btn-primary btn-sm">Buy Now</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="page-content">
                    {{ $slot }}
            </div>
            <div class="page-rightbar">
                <div class="card card-compact bg-white border">
                    <div class="card-body">
                        <h2 class="card-title">Shoes!</h2>
                        <p>If a dog chews shoes whose shoes does he choose?</p>
                        <div class="card-actions justify-end">
                            <button class="btn btn-primary btn-sm">Buy Now</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="page-footer mx-auto max-w-7xl p-2 mt-auto w-full prose prose-sm">
            {!! $currentConference?->getMeta('page_footer') !!}
        </div>
    </div>

</body>

</html>
