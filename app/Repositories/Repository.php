<?php

namespace App\Repositories;

use App\Repositories\Submission\SubmissionRepository;

final class Repository
{
    public static function submission(): SubmissionRepository
    {
        return app(SubmissionRepository::class);
    }
}
