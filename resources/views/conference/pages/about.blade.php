<x-website::layouts.main>
    <div class="p-5">
        <x-website::breadcrumbs :breadcrumbs="$this->getBreadcrumbs()" />
        <div class="">
            <h1 class="text-heading">{{ $this->getTitle() }} </h1>
            <div class="user-content">
                {{ new Illuminate\Support\HtmlString($currentConference->getMeta('about')) }}
            </div>
        </div>
    </div>
</x-website::layouts.main>
