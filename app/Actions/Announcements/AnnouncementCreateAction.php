<?php

namespace App\Actions\Announcements;

use App\Models\Announcement;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class AnnouncementCreateAction
{
    use AsAction;

    public function handle($data, $sendEmail = false)
    {
        try {
            DB::beginTransaction();

            $data['created_by'] = auth()->user()->id;
            
            $announcement = Announcement::create($data);
            
            $announcement->setManyMeta(Arr::only($data, ['user_content', 'expires_at']));

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
