<?php

namespace App\Actions\Site;

use App\Models\Site;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class SiteUpdateAction
{
    use AsAction;

    public function handle(array $data): Site
    {
        try {
            DB::beginTransaction();

            $site = app()->getSite();

            if ($meta = data_get($data, 'meta')) {
                $site->setManyMeta($meta);
            }

            if ($settings = data_get($data, 'settings')) {
                $prefixedMeta = [];
                foreach ($settings as $key => $value) {
                    $prefixedMeta['settings.' . $key] = $value;
                }
                $site->setManyMeta($prefixedMeta);
            }

            $site->touch();

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }

        return $site;
    }
}
