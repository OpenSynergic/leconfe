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
    public function serializeValue($value): string
    {
        return $value->value;
    }
}
