<x-conference::layouts.main>
        <div class="card-body">
            <h1 class="card-title">{{ $currentStaticPage->title }}</h1>
            {{ new Illuminate\Support\HtmlString($currentStaticPage->getMeta('user_content')) }}
        </div>
</x-conference::layouts.main>