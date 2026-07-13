<?php

namespace App\Metable\DataType;

use Plank\Metable\DataType\HandlerInterface;

/**
 * Handle serialization of arrays.
 */
abstract class EnumHandler implements HandlerInterface
{
    /**
     * The name of the scalar data type.
     *
     * @var string
     */
    protected $type;

    /**
     * {@inheritdoc}
     */
    public function getDataType(): string
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function serializeValue(mixed $value): string
    {
        return $value->value;
    }

    public function getNumericValue(mixed $value): null|int|float
    {
        return null;
    }

    public function useHmacVerification(): bool
    {
        return false;
    }
}
