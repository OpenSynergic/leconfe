<div class="page-main mx-auto max-w-7xl flex flex-col lg:grid grid-cols-12 gap-2 grow w-full">
    @if(setting('sidebar') == \App\Models\Constants\SidebarPosition::Left || setting('sidebar') == \App\Models\Constants\SidebarPosition::Both)
        @include('website.layouts.leftbar')
    @endif
    <div class="page-content @if(setting('sidebar') != \App\Models\Constants\SidebarPosition::Both) !col-span-8 @endif">
        {{ $slot }}
    </div>
    @if(setting('sidebar') == \App\Models\Constants\SidebarPosition::Right || setting('sidebar') == \App\Models\Constants\SidebarPosition::Both)
        @include('website.layouts.rightbar')
    @endif
</div>