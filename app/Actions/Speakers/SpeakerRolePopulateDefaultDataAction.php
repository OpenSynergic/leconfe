<?php

namespace App\Actions\Speakers;

use App\Models\SpeakerRole;
use App\Models\Serie;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class SpeakerRolePopulateDefaultDataAction
{
    use AsAction;

    public function handle(Serie $serie): void
    {
        try {
            DB::beginTransaction();

            foreach ([
                'Keynote Speaker',
                'Plenary Speaker',
            ] as $speakerRole) {
                SpeakerRole::firstOrCreate([
                    'name' => $speakerRole,
                    'serie_id' => $serie->getKey(),
                ]);
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }
}
