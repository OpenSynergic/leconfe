<?php

namespace App\Actions\Announcements;

use App\Models\UserContent;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class AnnouncementCreateAction
{
    use AsAction;

    public function handle($data, $sendEmail = false)
    {
        try {
            DB::beginTransaction();

            $announcement = UserContent::create($data);

            $announcement->setManyMeta([
                'short_description' => $data['short_description'] ?? null,
                'user_content' => $data['user_content'] ?? null,
                'expires_at' => $data['expires_at'] ?? null,
            ]);

            if ($sendEmail) {
                // TODO Create a job to send email

            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }

        return $announcement;
    }
}
