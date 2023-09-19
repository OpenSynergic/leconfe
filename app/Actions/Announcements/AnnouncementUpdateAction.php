<?php

namespace App\Actions\Announcements;

use App\Models\Announcement;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
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

            $announcement->syncMeta(Arr::only($data, ['user_content', 'expires_at']));

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
