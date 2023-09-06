<?php

namespace App\Actions\Speakers;

use App\Models\Conference;
use App\Models\Participants\SpeakerPosition;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class SpeakerPositionPopulateDefaultDataAction
{
    use AsAction;

    public function handle(Conference $conference): void
    {
        try {
            DB::beginTransaction();

            foreach ([
                'Keynote Speaker',
                'Plenary Speaker',
            ] as $speakerPosition) {
                SpeakerPosition::firstOrCreate([
                    'name' => $speakerPosition,
                    'type' => 'speaker',
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
