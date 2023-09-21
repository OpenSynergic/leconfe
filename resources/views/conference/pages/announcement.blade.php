<x-conference::layouts.main>
    <div class="card-body text-gray-800">
        <p class="text-xs text-gray-500 font-medium">{{ $this->announcement->created_at->format('l, j F Y') }}</p>
        <h1 class="card-title">{{ $this->announcement->title }}</h1>
        <div class="user-content">
            {{ new Illuminate\Support\HtmlString($this->announcement->getMeta('user_content')) }}
        </div>
    </div>
</x-conference::layouts.main>
