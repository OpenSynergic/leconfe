@props([
    'label',
    'url',
])

<div>
    <a 
        href="{{ $url }}" 
        class="h-8 px-3 text-xs rounded-none btn btn-outline btn-primary min-h-8"
    >
        {{ $label }}
    </a>
</div>