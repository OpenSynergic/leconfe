<x-conference::layouts.main>
    <div class="card-body flex gap-3">
        <x-conference::breadcrumbs :breadcrumbs="$this->getBreadcrumbs()" />
        <h2 class="card-title">{{ $this->getTitle() }} </h2>

        <div class="user-content">
            {{ new Illuminate\Support\HtmlString($currentConference->getMeta('about')) }}
        </div>
    </div>
</x-conference::layouts.main>
