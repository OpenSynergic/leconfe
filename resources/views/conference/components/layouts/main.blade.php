<div class="page-main mx-auto max-w-7xl flex flex-col lg:grid grid-cols-12 gap-3 grow w-full">
    <x-conference::layouts.leftbar />
    <div class="page-content col-span-12">
        {{ $slot }}
    </div>
    <x-conference::layouts.rightbar />
</div>

