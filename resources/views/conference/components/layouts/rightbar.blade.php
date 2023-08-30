<div class="page-rightbar">
    <div class="space-y-2">
        @foreach(Block::getBlocks(position: 'right') as $block)
            @livewire($block) 
        @endforeach
    </div>
</div>