<?php

namespace App\Repositories\Submission;

use App\Models\Submission;

abstract class BaseSubmissionRepository
{
    public function create($data)
    {
        return Submission::create($data);
    }

    public function update(Submission $submission, array $data)
    {
        $submission->update($data);

        if (array_key_exists('meta', $data) && is_array($data['meta'])) {
            $submission->setManyMeta($data['meta']);
        }

        return $submission;
    }

    public function delete(Submission $submission)
    {
        $submission->delete();
    }
}
