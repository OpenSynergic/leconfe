<x-website::layouts.main>
    <div class="card-body space-y-2">
        <x-website::breadcrumbs :breadcrumbs="$this->getBreadcrumbs()" />
        <h2 class="card-title">{{ $this->getTitle() }}</h2>
        <div class="user-content">
            {{ new Illuminate\Support\HtmlString($currentConference->getMeta('privacy_statement')) }}
        </div>
    </div>
</x-website::layouts.main>
