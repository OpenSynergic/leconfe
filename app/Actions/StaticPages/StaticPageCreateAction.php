<?php

namespace App\Actions\StaticPages;

use App\Models\StaticPage;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class StaticPageCreateAction
{
    use AsAction;

    public function handle($data, $sendEmail = false)
    {
        try {
            DB::beginTransaction();

            $data['created_by'] = auth()->user()->id;

            $staticPage = StaticPage::create($data);

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
