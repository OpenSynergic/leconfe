<div class="page-main mx-auto max-w-7xl flex flex-col lg:grid grid-cols-12 gap-2 grow w-full">
    @include('website.layouts.leftbar')
    <div class="page-content">
        {{ $slot }}
    </div>
    @include('website.layouts.rightbar')
</div>