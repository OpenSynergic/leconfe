<?php

namespace App\Actions\StaticPages;

use App\Models\Announcement;
use App\Models\Enums\ContentType;
use App\Models\StaticPage;
use Illuminate\Support\Arr;
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
            
            $staticPage->setManyMeta(Arr::only($data, ['user_content']));

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
