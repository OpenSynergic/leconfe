<?php

namespace App\Actions\Submissions;

use App\Models\Submission;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class SubmissionUpdateAction
{
    use AsAction;

    public function handle(array $data, Submission $submission): Submission
    {
        try {
            DB::beginTransaction();

            $submission->update($data);

            if (array_key_exists('meta', $data) && is_array($data['meta'])) {
                $submission->setManyMeta($data['meta']);
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }

        return $submission;
    }
}
