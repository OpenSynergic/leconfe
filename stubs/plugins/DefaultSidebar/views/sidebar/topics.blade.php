@if ($topics->isNotEmpty())
    <x-website::sidebar class="sidebar-submit-now" :id="$id">
        <h2 class="text-heading">Topics</h2>
        <div class="card card-compact bg-white border">
            <div class="card-body">
                <div class="inline-flex w-full flex-wrap gap-2">
                    @foreach ($topics as $topic)
                        <a href="#" class="badge badge-ghost badge-sm">
                            {{ $topic->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </x-website::sidebar>
@endif
