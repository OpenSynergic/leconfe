<ul>
    @forelse ($getState() ?? [] as $item)
        <li>{!! $item !!}</li>     
    @empty
        <li class="text-xs text-gray-400">No items</li>
    @endforelse
</ul>
