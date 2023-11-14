<?php

namespace App\Actions\Conferences;

use Illuminate\Support\Facades\File;
use Lorisleiva\Actions\Concerns\AsAction;

class AppearanceUpdateAction
{
    use AsAction;

    public function handle(array $data)
    {
        if (data_get($data, 'font')) {
            $style = File::get(base_path('resources/website/css/website.css'));

            $pattern = "/--font-family\s*:\s*'([\s*a-zA-Z0-9]+)'/";
            $output = preg_replace($pattern, "--font-family: '{$data['font']}'", $style);

            File::put(base_path('resources/website/css/website.css'), $output);
        }

        if (data_get($data, 'accent_color')) {
            $config = File::get(base_path('resources/website/css/tailwind.config.js'));

            $pattern = "/primary\s*:\s*'([#a-zA-Z0-9]+)'/";
            $output = preg_replace($pattern, "primary: '{$data['accent_color']}'", $config);

            File::put(base_path('resources/website/css/tailwind.config.js'), $output);
        }
    }
}
