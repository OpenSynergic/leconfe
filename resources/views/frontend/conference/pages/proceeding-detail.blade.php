<x-website::layouts.main>
    <div>
        <div class="mb-6">
            <x-website::breadcrumbs :breadcrumbs="$this->getBreadcrumbs()" />
        </div>
        <div class="mb-6">
            <x-conference::current-proceeding :proceeding="$proceeding" />
        </div>
        @if ($proceeding->submissions()->exists())
            <div class="mt-6">
                <x-conference::list-articles :articles="$articles"/>
            </div>
        @endif
    </div>
</x-website::layouts.main>