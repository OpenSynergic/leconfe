<?php

namespace App\Actions\Participants;

use App\Models\Participant;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class ParticipantUpdateAction
{
    use AsAction;

    public function handle(Participant $participant, array $data): Participant
    {
        try {
            DB::beginTransaction();

            $participant->update($data);

            if (data_get($data, 'meta')) {
                $participant->setManyMeta(data_get($data, 'meta'));
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }

        return $participant;
    }
}
