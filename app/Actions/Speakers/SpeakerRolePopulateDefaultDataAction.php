<?php

namespace App\Actions\Speakers;

use Throwable;
use App\Models\ScheduledConference;
use App\Models\SpeakerRole;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class SpeakerRolePopulateDefaultDataAction
{
    use AsAction;

    public function handle(ScheduledConference $scheduledConference): void
    {
        try {
            DB::beginTransaction();

            foreach ([
                'Keynote Speaker',
                'Plenary Speaker',
            ] as $speakerRole) {
                SpeakerRole::firstOrCreate([
                    'name' => $speakerRole,
                    'scheduled_conference_id' => $scheduledConference->getKey(),
                ]);
            }

            DB::commit();
        } catch (Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }
}
