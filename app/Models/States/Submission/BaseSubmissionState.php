<?php

namespace App\Models\States\Submission;

use App\Models\States\Interfaces\SubmissionStateInterface;
use App\Models\Submission;

class BaseSubmissionState implements SubmissionStateInterface
{
    public function __construct(public Submission $submission)
    {
    }

    public function fulfill(): void
    {
        throw new \Exception('Cannot fulfill submission');
    }

    public function acceptAbstract(): void
    {
        throw new \Exception('Cannot accept abstract');
    }

    public function acceptAbstractaccept(): void
    {
        throw new \Exception('Cannot accept');
    }

    public function publish(): void
    {
        throw new \Exception('Cannot publish');
    }

    public function unpublish(): void
    {
        throw new \Exception('Cannot unpublish');
    }

    public function decline(): void
    {
        throw new \Exception('Cannot decline');
    }

    public function skipReview(): void
    {
        throw new \Exception('Cannot skip review');
    }

    public function requestRevision(): void
    {
        throw new \Exception('Cannot request revision');
    }

    public function withdraw(): void
    {
        throw new \Exception('Cannot withdraw');
    }
}
