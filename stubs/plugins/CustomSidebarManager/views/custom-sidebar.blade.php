<x-website::sidebar :id="$id" class="sidebar-{{ Str::snake($name, '-') }}">
    @if ($showName)
        <h2 class="text-heading">{{ $name }}</h2>
    @endif
    <div class="card card-compact bg-white border">
        <div class="card-body">
            <div class="user-content">
                {!! $content !!}
            </div>
        </div>
    </div>
</x-website::sidebar>
