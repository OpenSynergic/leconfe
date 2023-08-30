<x-conference::layouts.main>
        <div class="card-body" style="font-family: Helvetica">
            <h1 class="card-title">{{ $contentTypeSlug }}</h1>
            @foreach ($staticPageList as $staticPage)    
                <a href="{{ route('livewirePageGroup.current-conference.pages.static-page', ['content_type' => $contentTypeSlug, 'user_content' => $staticPage->id]) }}" class="block max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
                    <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{{ $staticPage->title }}</h5>
                    <p class="font-normal text-gray-700 dark:text-gray-400">{{ new Illuminate\Support\HtmlString($staticPage->getMeta('short_description')) }}</p>
                </a>
            @endforeach
        </div>
</x-conference::layouts.main>