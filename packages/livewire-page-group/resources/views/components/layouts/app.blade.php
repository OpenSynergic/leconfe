<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $livewire?->getTitle() ?? '' }}</title>
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
  <style>
      [x-cloak] { display: none !important; }
  </style>
</head>
<body class="antialiased h-screen overflow-y-scroll">
  {{ $slot }}
</body>
</html>