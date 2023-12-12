<?php

namespace App\Metable\DataType;

use Carbon\Carbon;
use DateTimeInterface;
use Plank\Metable\DataType\DateTimeHandler as PlankDateTimeHandler;

/**
 * Handle serialization of DateTimeInterface objects.
 */
class DateTimeHandler extends PlankDateTimeHandler
{
    /**
     * The date format to use for serializing.
     *
     * @var string
     */
    protected $format = 'Y-m-d H:i:s.uO';

    /**
     * {@inheritdoc}
     */
    public function unserializeValue(string $value)
    {
        return Carbon::createFromFormat($this->format, $value);
    }
}
