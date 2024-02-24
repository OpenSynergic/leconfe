<x-website::block :id="$id">
    <h2 class="text-heading px-2 mb-1">{{ $name }}</h2>
    <div class="card card-compact bg-white border">
        <div class="card-body">
            <div class="user-content">
                {!! $content !!}
            </div>
        </div>
    </div>
</x-website::block>