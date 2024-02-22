<div class="page-leftbar">
    <div class="space-y-4">
        @foreach(\App\Facades\Block::getBlocks(position: 'left') as $block)
            {!! $block->toHtml() !!}
        @endforeach
    </div>
</div>
