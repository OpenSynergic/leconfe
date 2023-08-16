@if($currentConference?->hasMeta('page_footer'))
<div class="page-footer mx-auto max-w-7xl p-2 mt-auto w-full prose prose-sm">
    {!! $currentConference?->getMeta('page_footer') !!}
</div>
@endif