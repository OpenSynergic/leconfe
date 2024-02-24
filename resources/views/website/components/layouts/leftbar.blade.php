<div class="page-leftbar">
    <div class="space-y-4">
        @foreach (\App\Facades\Block::getBlocks(position: 'left') as $block)
            {!! $block->toHtml() !!}
        @endforeach
        <div id="block_menu" class="block">
            <h2 class="text-heading px-2 mb-1">Menu</h2>
            <div class="card card-compact bg-white border">
                <div class="card-body">
                    <div class="user-content">
                        <div class="not-prose -m-4">
                            <ul class="font-medium text-gray-900">
                                @for ($i = 0; $i < 5; $i++)
                                    <li>
                                        <a href="#" class="block w-full px-4 py-2 font-medium text-left border-b border-gray-200 cursor-pointer hover:bg-gray-100 hover:bg-primary hover:text-white">Menu {{ $i+1 }}</a>
                                    </li>
                                @endfor
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
