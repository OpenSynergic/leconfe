<?php

namespace App\Actions\StaticPages;

use App\Models\Announcement;
use App\Models\Enums\ContentType;
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

            $staticPage = StaticPage::create($data);

            unset($data['title']);
            unset($data['content_type']);
            
            $staticPage->setManyMeta($data);

            if ($sendEmail) {
                // TODO Create a job to send email

            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }

        return $staticPage;
    }
}
