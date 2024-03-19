@props([
    'class' => null,
    'id',
])

<div  
    {{ 
        $attributes->class([
            'block',
            $class,    
        ])->merge([
            'id' => 'block_' . $id,
        ])
    }}
    >
    {{ $slot }}
</div>