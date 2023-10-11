<div class="page-rightbar">
    <div class="space-y-6">
        @foreach(\App\Facades\Block::getBlocks(position: 'right') as $block)
            @livewire($block)
        @endforeach
    </div>
</div>
