<?php

namespace App\Actions\StaticPages;

use App\Models\StaticPage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class StaticPageUpdateAction
{
    use AsAction;

    public function handle(StaticPage $staticPage, array $data): Model
    {
        try {
            DB::beginTransaction();

            $staticPage->update($data);

            if (data_get($data, 'meta')) {
                $staticPage->setManyMeta(data_get($data, 'meta'));
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }

        return $staticPage;
    }
}
