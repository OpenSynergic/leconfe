@props([
    'sidebars' => []
])

<div class="page-rightbar mx-1 sm:mx-none">
    <div class="space-y-4">
        @foreach($sidebars as $sidebar)
            {{ $sidebar }}
        @endforeach
    </div>
</div>
