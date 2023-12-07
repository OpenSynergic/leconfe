<?php

namespace App\Actions\Submissions;

use App\Models\Role;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class SubmissionAssignParticipantAction
{
    use AsAction;

    public function handle(Submission $submission, User $user, Role $role)
    {
        try {
            DB::beginTransaction();
            $submissionParticipant = $submission->participants()->updateOrCreate([
                'user_id' => $user->getKey(),
            ], [
                'user_id' => $user->getKey(),
                'role_id' => $role->getKey(),
            ]);
            DB::commit();
            return $submissionParticipant;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
