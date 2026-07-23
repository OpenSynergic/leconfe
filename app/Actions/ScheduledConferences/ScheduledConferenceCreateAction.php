<?php

namespace App\Actions\ScheduledConferences;

use Throwable;
use App\Models\ScheduledConference;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class ScheduledConferenceCreateAction
{
    use AsAction;

    public function handle(array $data): ScheduledConference
    {
        try {
            DB::beginTransaction();

            $scheduledConference = ScheduledConference::create($data);

            if (data_get($data, 'meta')) {
                $scheduledConference->setManyMeta($data['meta']);
            }

            DB::commit();
        } catch (Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        return $scheduledConference;
    }
}
