<x-website::layouts.main>
    <div class="card-body">
        <x-website::breadcrumbs :breadcrumbs="$this->getBreadcrumbs()" />
            <div class="flex flex-col -space-y-3 mb-6">
            <h2 class="text-heading mt-6 ms-1">{{ $this->getTitle() }} </h2>
            <div class="user-content">
                {{ new Illuminate\Support\HtmlString($currentConference->getMeta('about')) }}
            </div>
        </div>

    </div>
</x-conference::layouts.main>
