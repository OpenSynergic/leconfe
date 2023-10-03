<?php

namespace App\Actions\Submissions;

use App\Models\Participant;
use App\Models\ParticipantPosition;
use App\Models\Submission;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class SubmissionAssignParticipantAction
{
    use AsAction;

    public function handle(Submission $submission, Participant $participant, ParticipantPosition $participantPosition)
    {
        try {
            DB::beginTransaction();
            $submissionParticipant = $submission->participants()->updateOrCreate([
                'participant_id' => $participant->id,
            ], [
                'participant_id' => $participant->id,
                'participant_position_id' => $participantPosition->id,
            ]);
            DB::commit();
            return $submissionParticipant;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
