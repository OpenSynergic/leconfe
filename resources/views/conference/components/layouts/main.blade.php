@php
    $leftSidebarActive = \App\Models\Constants\SidebarPosition::isLeft();
    $rightSidebarActive = \App\Models\Constants\SidebarPosition::isRight();
    $bothSidebarActive = \App\Models\Constants\SidebarPosition::isBoth();
    $sidebarActive = $leftSidebarActive || $rightSidebarActive;
@endphp
<div class="page-main mx-auto max-w-7xl flex flex-col lg:grid grid-cols-12 gap-3 grow w-full">
    @if($leftSidebarActive || $bothSidebarActive)
        <x-conference::layouts.leftbar />
    @endif
    <div class="page-content {{ $sidebarActive ? '!col-span-9' : ($bothSidebarActive ? '' : '!col-span-12') }}">
        {{ $slot }}
    </div>
    @if($rightSidebarActive || $bothSidebarActive)
        <x-conference::layouts.rightbar />
    @endif
</div>