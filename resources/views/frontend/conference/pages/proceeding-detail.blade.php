<x-website::layouts.main>
    <div class="space-y-6">
        <x-website::breadcrumbs :breadcrumbs="$this->getBreadcrumbs()" />
        @if ($this->canPreview())
            <x-website::preview-alert />
        @endif
        <x-conference::current-proceeding :proceeding="$proceeding" />
        @if ($articles->exists())
            <x-conference::list-articles :articles="$articles->get()"/>
        @endif
    </div>
</x-website::layouts.main>