<?php

namespace App\Constants;

final class SubmissionStatusRecommendation
{
    public const ACCEPT = 'Accept';

    public const DECLINE = 'Decline';

    public const REVISION_REQUIRED = 'Revision Required';

    public static function list()
    {
        return [
            self::ACCEPT => self::ACCEPT,
            self::DECLINE => self::DECLINE,
            self::REVISION_REQUIRED => self::REVISION_REQUIRED,
        ];
    }
}
