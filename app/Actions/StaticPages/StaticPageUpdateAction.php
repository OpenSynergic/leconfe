<?php

namespace App\Actions\StaticPages;

use App\Models\StaticPage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class StaticPageUpdateAction
{
    use AsAction;

    public function handle(array $data, StaticPage $staticPage): Model
    {
        try {
            DB::beginTransaction();

            $staticPage->update($data);

            $staticPage->syncMeta(Arr::only($data, ['user_content']));

            // if ($sendEmail) {
            //     // TODO Create a job to send email

            // }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }

        return $staticPage;
    }
}
