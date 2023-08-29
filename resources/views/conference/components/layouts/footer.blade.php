@if ($currentConference?->hasMeta('page_footer'))
    <div class="page-footer mt-auto w-full">
        {!! $currentConference?->getMeta('page_footer') !!}
    </div>
@endif
