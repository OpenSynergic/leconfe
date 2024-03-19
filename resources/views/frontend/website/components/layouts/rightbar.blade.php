<div class="page-rightbar mx-1 sm:mx-none">
    <div class="space-y-4">
        @foreach(\App\Facades\Block::getBlocks(position: 'right') as $block)
            {!! $block->toHtml() !!}
        @endforeach
    </div>
</div>
