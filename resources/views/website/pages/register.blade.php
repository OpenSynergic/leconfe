<x-conference::layouts.main>
        <div class="card-body space-y-2">
            <x-conference::breadcrumbs :breadcrumbs="$this->getBreadcrumbs()" />
            <h2 class="card-title">{{$this->getTitle()}}</h2>
        </div>
</x-conference::layouts.main>