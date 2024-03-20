<div class="block space-y-2">
    @if (count($topics) > 0)
    <h2 class="ml-1 text-lg text-heading">Topics</h2>
    <div class="card card-compact bg-white border">
        <div class="card-body">
          <div class="inline-flex w-full flex-wrap gap-2">
            @foreach ($topics as $topic)
            <div class="badge badge-outline text-xs border border-gray-300 h-6 text-xs text-secondary">{{ $topic->name }}</div>
            @endforeach
        </div>
    </div>
</div>
@endif
</div>
