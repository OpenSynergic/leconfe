<div class="page-rightbar mx-1 sm:mx-none">
    <div class="space-y-4">
        @foreach(\App\Facades\SidebarFacade::get(false) as $sidebar)
            {{ $sidebar }}
        @endforeach
    </div>
</div>
