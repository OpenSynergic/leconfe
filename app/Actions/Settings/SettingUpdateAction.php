<?php

namespace App\Actions\Settings;

use Lorisleiva\Actions\Concerns\AsAction;

class SettingUpdateAction
{
    use AsAction;

    public function handle(array $data)
    {
        return setting($data);
    }
}
