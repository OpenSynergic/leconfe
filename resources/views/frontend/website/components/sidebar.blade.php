@props([
    'id',
])

<div  
    {{ 
        $attributes
        ->class(['sidebar'])
        ->merge([
            'id' => 'sidebar-' . $id,
        ])
    }}
    >
    {{ $slot }}
</div>