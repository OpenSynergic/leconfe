<?php

namespace App\Actions\Site;

use App\Facades\Settings;
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

            $site->touch();

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }

        return $site;
    }
}
