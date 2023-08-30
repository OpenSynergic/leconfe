<div class="page-leftbar">
    <div class="space-y-2">
        @foreach(Block::getBlocks(position: 'left') as $block)
            @livewire($block) 
        @endforeach
    </div>
</div>
