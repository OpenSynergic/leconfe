<x-conference::layouts.main>
        <div class="card-body">
            <h1 class="text-2xl">{{ $contentTitle }}</h1>
            @foreach ($staticPageList as $staticPage)    
                <div class="block w-full px-3 py-2 bg-white border border-gray-300 rounded-md">
                    <h5 class="text-xl">{{ $staticPage->title }}</h5>
                    <p class="font-normal">{{ new Illuminate\Support\HtmlString($staticPage->getMeta('short_description') ? $staticPage->getMeta('short_description') : '<p>No description added</p>') }}</p>
                    <div class="card-actions justify-end mb-2">
                        <a href="{{ route('livewirePageGroup.current-conference.pages.static-page', ['content_type' => $contentTypeSlug, 'user_content' => $staticPage->id]) }}" class="btn btn-primary font-normal btn-sm">Read more</a>
                    </div>
                </div>
            @endforeach
        </div>
</x-conference::layouts.main>