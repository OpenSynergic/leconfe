<div class="page-main mx-auto max-w-7xl flex flex-col lg:grid grid-cols-12 gap-3 grow w-full">
    @if(\App\Models\Constants\SidebarPosition::isLeft())
        <x-conference::layouts.leftbar />
    @endif
    <div class="page-content">
        {{ $slot }}
    </div>
    @if(\App\Models\Constants\SidebarPosition::isRight())
        <x-conference::layouts.rightbar />
    @endif
</div>