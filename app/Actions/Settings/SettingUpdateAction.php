<?php

namespace App\Actions\Settings;

use Lorisleiva\Actions\Concerns\AsAction;
use Akaunting\Setting\Facade as Setting;
use Illuminate\Support\Arr;

class SettingUpdateAction
{
    use AsAction;

    public function handle(array $data)
    {
        return setting(Arr::dot($data));
    }
}
