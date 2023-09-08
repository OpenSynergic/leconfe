<?php

namespace App\Actions\Participants;

use App\Models\Participants\Participant;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class ParticipantCreateAction
{
    use AsAction;

    public function handle(array $data)
    {
        try {
            DB::beginTransaction();

            $participant = Participant::create($data);

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
