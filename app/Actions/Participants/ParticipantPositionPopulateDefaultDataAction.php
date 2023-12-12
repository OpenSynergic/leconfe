<?php

namespace App\Actions\Participants;

use App\Models\Conference;
use App\Models\Enums\UserRole;
use App\Models\ParticipantPosition;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class ParticipantPositionPopulateDefaultDataAction
{
    use AsAction;

    public function handle(Conference $conference): void
    {
        try {
            DB::beginTransaction();

            ParticipantPosition::firstOrCreate([
                'name' => UserRole::Reviewer->value,
                'type' => 'reviewer',
                'conference_id' => $conference->getKey(),
            ]);

            foreach ([
                UserRole::Editor->value,
            ] as $authorPosition) {
                ParticipantPosition::firstOrCreate([
                    'name' => $authorPosition,
                    'type' => 'editor',
                    'conference_id' => $conference->getKey(),
                ]);
            }

            foreach ([
                UserRole::Author->value,
                'Co Author',
            ] as $authorPosition) {
                ParticipantPosition::firstOrCreate([
                    'name' => $authorPosition,
                    'type' => 'author',
                    'conference_id' => $conference->getKey(),
                ]);
            }

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
