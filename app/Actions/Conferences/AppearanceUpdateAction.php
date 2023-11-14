<?php

namespace App\Actions\Conferences;

use Illuminate\Support\Facades\File;
use Lorisleiva\Actions\Concerns\AsAction;

class AppearanceUpdateAction
{
    use AsAction;

    public function handle(array $data)
    {
        $changesCount = 0;

        $style = File::get(base_path('resources/website/css/website.css'));
        $pattern = "/--font-family\s*:\s*'([\s*a-zA-Z0-9]+)'/";
        preg_match($pattern, $style, $matches);
        if ($matches[1] != data_get($data, 'font')) {
            $output = preg_replace($pattern, "--font-family: '{$data['font']}'", $style);
            File::put(base_path('resources/website/css/website.css'), $output);

            ++$changesCount;
        }

        $config = File::get(base_path('resources/website/css/tailwind.config.js'));
        $pattern = "/primary\s*:\s*'([#a-zA-Z0-9]+)'/";
        preg_match($pattern, $config, $matches);
        if ($matches[1] != data_get($data, 'accent_color')) {
            $output = preg_replace($pattern, "primary: '{$data['accent_color']}'", $config);
            File::put(base_path('resources/website/css/tailwind.config.js'), $output);

            ++$changesCount;
        }

        if (app()->isProduction() && $changesCount > 0) {
            // Then run npm run build?
        }
    }
}
