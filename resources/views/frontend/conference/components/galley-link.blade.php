@props([
    'galley',
])

<div>
    <a 
        href="{{ $galley->remote_url ?? route('submission-files.view', $galley->file->media->uuid) }}" 
        class="galley-link btn btn-outline btn-primary btn-sm"
    >
        {{ $galley->label }}
    </a>
</div>