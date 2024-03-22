<?php

namespace App\Actions\Speakers;

use App\Models\SpeakerRole;
use App\Models\Conference;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class SpeakerRolePopulateDefaultDataAction
{
    use AsAction;

    public function handle(Conference $conference): void
    {
        try {
            DB::beginTransaction();

            foreach ([
                'Keynote Speaker',
                'Plenary Speaker',
            ] as $speakerRole) {
                SpeakerRole::firstOrCreate([
                    'name' => $speakerRole,
                    'conference_id' => $conference->getKey(),
                ]);
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }
}
