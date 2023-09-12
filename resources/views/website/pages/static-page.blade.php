<x-conference::layouts.main>
        <div class="card-body text-gray-800">
            <p class="text-xs text-gray-500 font-medium">{{ $currentStaticPage->created_at->format('l, j F Y') }}</p>
            <h1 class="card-title">{{ $currentStaticPage->title }}</h1>
            {{ new Illuminate\Support\HtmlString($currentStaticPage->getMeta('user_content')) }}
        </div>
</x-conference::layouts.main>