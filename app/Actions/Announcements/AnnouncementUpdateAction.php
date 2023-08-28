<?php

namespace App\Actions\Announcements;

use App\Models\UserContent;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class AnnouncementUpdateAction
{
    use AsAction;

    public function handle(array $data, UserContent $userContent): UserContent
    {
        try {
            DB::beginTransaction();

            $userContent->update($data);

            $userContent->syncMeta([
                'short_description' => $data['short_description'] ?? null,
                'user_content' => $data['user_content'] ?? null,
                'expires_at' => $data['expires_at'] ?? null,
            ]);

            // if ($sendEmail) {
            //     // TODO Create a job to send email

            // }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }

        return $userContent;
    }
}
