<x-conference::layouts.main>
    <div class="card-body flex gap-3">
        <x-conference::breadcrumbs :breadcrumbs="$this->getBreadcrumbs()" />
        <div class="flex justify-between">
            <div class="flex flex-col space-y-2">
                <h2 class="card-title">{{ $this->getTitle() . ' ' . $currentConference->name }} </h2>
            </div>
        </div>

        <div class="flex gap-2">
            <div class="text-gray-700 flex flex-col gap-2">
                <p class="font-normal text-sm">{!! html_entity_decode($currentConference->getMeta('about')) ?? '' !!}</p>
            </div>
        </div>
    </div>
</x-conference::layouts.main>
