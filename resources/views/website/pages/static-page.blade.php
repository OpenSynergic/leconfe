<x-website::layouts.main>
        <div class="card-body text-gray-800">
            <h1 class="card-title">{{ $this->staticPage->title }}</h1>
            <div 
                class="user-content"
            >
                {{ new Illuminate\Support\HtmlString($this->staticPage->getMeta('content')) }}
            </div>
        </div>
</x-website::layouts.main>