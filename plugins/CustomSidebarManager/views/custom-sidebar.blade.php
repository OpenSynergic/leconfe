<x-website::block :id="$id">
    <div class="card card-compact bg-white border">
        <div class="card-body">
            <h2 class="card-title border-b">
                {{ $name }}
            </h2>
            <div class="user-content">
                {!! $content !!}
            </div>
        </div>
    </div>
</x-website::block>