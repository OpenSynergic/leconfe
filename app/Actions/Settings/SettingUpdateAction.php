<?php

namespace App\Actions\Settings;

use Illuminate\Support\Arr;
use Lorisleiva\Actions\Concerns\AsAction;

class SettingUpdateAction
{
    use AsAction;

    public function handle(array $data)
    {
        return setting(Arr::dot($data));
    }
}
