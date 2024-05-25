<div class="block space-y-2">
    @if (count($topics) > 0)
        <h2 class="ml-1 text-lg text-heading">Topics</h2>
        <div class="bg-white border card card-compact">
            <div class="card-body">
                <div class="inline-flex flex-wrap w-full gap-2">
                    @foreach ($topics as $topic)
                        <a href="{{ route('livewirePageGroup.conference.pages.proceedings', $topic->slug) }}" class="h-6 text-xs border border-gray-300 badge badge-outline text-secondary">
                            {{ $topic->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>
