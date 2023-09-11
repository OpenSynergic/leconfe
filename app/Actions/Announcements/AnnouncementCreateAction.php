<?php

namespace App\Actions\Announcements;

use App\Models\Announcement;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class AnnouncementCreateAction
{
    use AsAction;

    public function handle($data, $sendEmail = false)
    {
        try {
            DB::beginTransaction();

            unset($data['common_tags']);

            $announcement = Announcement::create($data);

            unset($data['title']);
            unset($data['content_type']);
            unset($data['send_email']);

            $data['author'] = auth()->user()->id;
            
            $announcement->setManyMeta($data);

            // if ($sendEmail) {
            //     // TODO Create a job to send email

            // }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }

        return $announcement;
    }
}
