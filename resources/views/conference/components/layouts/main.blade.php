@php
    use \App\Models\Enums\SidebarPosition;
    $leftSidebarActive = $rightSidebarActive = $bothSidebarActive = $sidebarActive = false;
    
    if (isset($currentConference)) {
        $sidebarActive = isSidebar($currentConference, SidebarPosition::Left) || isSidebar($currentConference, SidebarPosition::Right);
        $bothSidebarActive = isSidebar($currentConference, SidebarPosition::Both);
    }
    
    function isSidebar($currentConference, $position): bool {
        return $currentConference->getMeta('sidebar') == $position->getValue();
    }
@endphp

<div class="page-main mx-auto max-w-7xl flex flex-col lg:grid grid-cols-12 gap-3 grow w-full">
    @if($leftSidebarActive || $bothSidebarActive)
        <x-conference::layouts.leftbar />
    @endif
    <div @class([
            'page-content col-span-12',
            '!col-span-9' => $sidebarActive,
            '!col-span-12' => !$bothSidebarActive,
        ])>
        {{ $slot }}
    </div>
    @if($rightSidebarActive || $bothSidebarActive)
        <x-conference::layouts.rightbar />
    @endif
</div>

