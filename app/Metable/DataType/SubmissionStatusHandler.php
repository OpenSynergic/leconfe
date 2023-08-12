<?php

namespace App\Metable\DataType;

use App\Models\Enums\SubmissionStatus;

/**
 * Handle serialization of arrays.
 */
class SubmissionStatusHandler extends EnumHandler
{
    /**
     * {@inheritdoc}
     */
    protected $type = 'submission_status';

    /**
     * {@inheritdoc}
     */
    public function canHandleValue($value): bool
    {
        return $value instanceof SubmissionStatus;
    }

    /**
     * {@inheritdoc}
     */
    public function unserializeValue(string $value)
    {
        return SubmissionStatus::from($value);
    }
}
