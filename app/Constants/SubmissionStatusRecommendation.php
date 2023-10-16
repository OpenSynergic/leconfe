<?php

namespace App\Constants;

final class SubmissionStatusRecommendation
{
    public const ACCEPT = 'Accept';

    public const DECLINE = 'Decline';

    public const REVISION_REQUIRED = "Revision Required";

    public static function list()
    {
        return [
            static::ACCEPT => static::ACCEPT,
            static::DECLINE => static::DECLINE,
            static::REVISION_REQUIRED => static::REVISION_REQUIRED
        ];
    }
}
