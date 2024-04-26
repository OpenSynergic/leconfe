<?php

namespace App\Actions\Settings;

use App\Facades\Settings;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class SettingUpdateAction
{
    use AsAction;

    public function handle(array $data)
    {
        try {
            DB::beginTransaction();
            foreach ($data as $key => $value) {
                Settings::set($key, $value);
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
        return true;
    }
}
