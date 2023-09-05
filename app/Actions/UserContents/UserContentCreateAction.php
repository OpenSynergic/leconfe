<?php

namespace App\Actions\UserContents;

use App\Models\Announcement;
use App\Models\Enums\ContentType;
use App\Models\StaticPage;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class UserContentCreateAction
{
    use AsAction;

    public function handle($data, $sendEmail = false)
    {
        try {
            DB::beginTransaction();

            switch ($data['content_type']) {
                case ContentType::Announcement:
                    $userContent = Announcement::create($data);
                    break;
                
                case ContentType::StaticPage:
                    $userContent = StaticPage::create($data);
                    break;
                
                default:
                    break;
            }

            unset($data['title']);
            unset($data['content_type']);
            if (isset($data['send_email'])) {
                unset($data['send_email']);
            }
            
            $userContent->setManyMeta($data);

            if ($sendEmail) {
                // TODO Create a job to send email

            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }

        return $userContent;
    }
}
