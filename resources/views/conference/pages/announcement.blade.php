<x-conference::layouts.main>
        <div class="card-body text-gray-800">
            <p class="text-xs text-gray-500 font-medium">{{ $this->record->created_at->format('l, j F Y') }}</p>
            <h1 class="card-title">{{ $this->record->title }}</h1>
            <div 
                class="prose prose-sm prose-a:text-primary prose-a:underline hover:prose-a:text-primary"
            >
                {{ new Illuminate\Support\HtmlString($this->record->getMeta('user_content')) }}
            </div>
        </div>
</x-conference::layouts.main>