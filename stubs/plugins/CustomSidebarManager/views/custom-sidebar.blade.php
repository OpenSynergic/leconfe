<x-website::sidebar :id="$id">
    @if ($showName)
        <h2 class="text-heading px-2 mb-1">{{ $name }}</h2>
    @endif
    <div class="card card-compact bg-white border">
        <div class="card-body">
            <div class="user-content">
                {!! $content !!}
            </div>
        </div>
    </div>
</x-website::sidebar>
