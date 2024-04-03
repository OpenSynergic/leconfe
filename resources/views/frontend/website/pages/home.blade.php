<x-website::layouts.main>
    <div class="space-y-2">
        <div class="description user-content">
            {{ new Illuminate\Support\HtmlString($site->getMeta('about')) }}
        </div>
    </div>
</x-website::layouts.main>
