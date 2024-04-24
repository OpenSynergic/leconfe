<?php

namespace App\Actions\Settings;

use App\Models\Setting;
use App\Facades\Settings;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class SettingUpdateAction
{
    use AsAction;

    public function handle(array $data)
    {
        try {
            DB::beginTransaction();
            $setting = Setting::query()->first();
            foreach ($data as $key => $value) {
                Settings::set($key, $value);
            }
            $setting->touch();
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
        return $setting;
    }
}
