<x-conference::layouts.main>
        <div class="card-body">
            <p class="text-xs text-gray-400 font-medium">{{ $currentStaticPage->created_at->format('l, j F Y') }}</p>
            <h1 class="card-title">{{ $currentStaticPage->title }}</h1>
            {{ new Illuminate\Support\HtmlString($currentStaticPage->getMeta('user_content')) }}
        </div>
</x-conference::layouts.main>