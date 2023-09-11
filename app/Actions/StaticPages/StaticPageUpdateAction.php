<?php

namespace App\Actions\StaticPages;

use App\Models\StaticPage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class StaticPageUpdateAction
{
    use AsAction;

    public function handle(array $data, StaticPage $staticPage): Model
    {
        try {
            DB::beginTransaction();

            unset($data['common_tags']);

            $staticPage->update($data);
            
            unset($data['title']);
            unset($data['path']);

            $staticPage->syncMeta($data);

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
