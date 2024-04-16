<x-website::layouts.main>
    <x-website::breadcrumbs :breadcrumbs="$this->getBreadcrumbs()" />
    <div class="">
        <h1 class="text-heading">{{ $this->getTitle() }} </h1>
        <div class="user-content">
            {{ new Illuminate\Support\HtmlString($about) }}
        </div>
    </div>
</x-website::layouts.main>
