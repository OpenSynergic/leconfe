@props([
    'class' => null,
    'id',
])

<div  
    {{ 
        $attributes->class([
            'sidebar',
            $class,    
        ])->merge([
            'id' => 'sidebar_' . $id,
        ])
    }}
    >
    {{ $slot }}
</div>