<?php

namespace App\Classes;

use Illuminate\Contracts\View\View;

class CustomBlock extends Block
{
    public function __construct(
        public string $id,
        public string $name,
        public string $content,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function render(): View
    {
        return view('test', [
            'content' => $this->content
        ]);
    }
}
