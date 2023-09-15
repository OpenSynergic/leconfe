<?php

namespace App\Actions\Announcements;

use App\Models\Announcement;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class AnnouncementUpdateAction
{
    use AsAction;

    public function handle(array $data, Announcement $announcement): Model
    {
        try {
            DB::beginTransaction();

            $announcement->update($data);
            
            unset($data['title']);
            unset($data['send_email']);

            $announcement->syncMeta($data);

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
