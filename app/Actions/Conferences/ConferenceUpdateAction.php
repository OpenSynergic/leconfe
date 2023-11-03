<?php

namespace App\Actions\Conferences;

use App\Models\Conference;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Lorisleiva\Actions\Concerns\AsAction;

class ConferenceUpdateAction
{
    use AsAction;

    public function handle(Conference $conference, array $data)
    {
        try {
            DB::beginTransaction();

            $conference->update($data);

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

            if (data_get($data, 'meta')) {
                $conference->setManyMeta(data_get($data, 'meta'));
            }

            if (data_get($data, 'active')) {
                ConferenceSetActiveAction::run($conference);
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }

        return $conference;
    }
}
