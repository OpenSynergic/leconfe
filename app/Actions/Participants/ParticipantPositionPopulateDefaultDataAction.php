<?php

namespace App\Actions\Participants;

use App\Models\Conference;
use App\Models\Participants\ParticipantPosition;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class ParticipantPositionPopulateDefaultDataAction
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
                ParticipantPosition::firstOrCreate([
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
