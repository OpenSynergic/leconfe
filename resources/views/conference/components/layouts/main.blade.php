
@php
    if(!function_exists('isSidebar')) {
        // If $currentConference is inside a function, it is undefined.
        function isSidebar($currentConference, $position): bool {
            return $currentConference->getMeta('sidebar') == $position->getValue();
        }
    }
    $leftSidebarActive = isSidebar($currentConference, \App\Models\Enums\SidebarPosition::Left);
    $rightSidebarActive = isSidebar($currentConference, \App\Models\Enums\SidebarPosition::Right);
    $bothSidebarActive = isSidebar($currentConference, \App\Models\Enums\SidebarPosition::Both);
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
