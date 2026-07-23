<?php

namespace App\Actions\Committees;

use Throwable;
use App\Models\CommitteeRole;
use App\Models\ScheduledConference;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class CommitteeRolePopulateDefaultDataAction
{
    use AsAction;

    public function handle(ScheduledConference $scheduledConference): void
    {
        try {
            DB::beginTransaction();

            foreach ([
                'Chair',
                'Member',
            ] as $committeeRole) {
                CommitteeRole::firstOrCreate([
                    'name' => $committeeRole,
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
