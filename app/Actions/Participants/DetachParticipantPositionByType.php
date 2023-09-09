<?php

namespace App\Actions\Participants;

use App\Models\Participants\Participant;
use Lorisleiva\Actions\Concerns\AsAction;

class DetachParticipantPositionByType
{
    use AsAction;

    public function handle(Participant $participant, string $positionType)
    {
        $positions = $participant
            ->positions()
            ->where('type', $positionType)
            ->get();

        return $participant->positions()->detach($positions);
    }
}
