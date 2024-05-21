<x-website::layouts.main>
    <div class="space-y-6">
        <x-website::breadcrumbs :breadcrumbs="$this->getBreadcrumbs()" />
        @if ($this->canPreview())
            <x-website::preview-alert />
        @endif
        <x-conference::proceeding :proceeding="$proceeding" />
    </div>
</x-website::layouts.main>