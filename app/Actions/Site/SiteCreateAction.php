<?php

namespace App\Actions\Site;

use App\Models\Site;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class SiteCreateAction
{
    use AsAction;

    public function handle(): Site
    {
        try {
            DB::beginTransaction();

            $site = Site::create();

            $site->setManyMeta([
                'name' => 'Leconfe',
                'page_footer' => view('examples.footer')->render(),
            ]);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }

        return $site;
    }
}
