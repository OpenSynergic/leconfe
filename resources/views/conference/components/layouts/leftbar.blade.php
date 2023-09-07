<div class="page-leftbar">
    <div class="space-y-2">
        @foreach(\App\Facades\Block::getBlocks(position: 'left') as $block)
            @livewire($block) 
        @endforeach
    </div>
</div>
