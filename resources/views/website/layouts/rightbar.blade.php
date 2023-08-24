<div class="page-rightbar">
    @foreach(Block::getBlocks(position: 'right') as $block)
        @livewire($block) 
    @endforeach
    {{-- <div class="block space-y-2">
        <h2 class="card-title">Dummy !</h2>
        <div class="card card-compact bg-white border">
            <div class="card-body">
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Earum facere rem, laboriosam corporis temporibus possimus in quas perspiciatis voluptatem totam harum voluptate consequatur maxime quidem quaerat laudantium dolorum incidunt dolorem.</p>
                <div class="card-actions justify-end">
                    <button class="btn btn-secondary btn-sm">Another Button</button>
                </div>
            </div>
        </div>
    </div> --}}
</div>