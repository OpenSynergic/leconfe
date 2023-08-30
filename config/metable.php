<?php

use App\Models\Meta;

return [
    /*
     * Model class to use for Meta.
     */
    'model' => Meta::class,

    /*
     * List of handlers for recognized data types.
     *
     * Handlers will be evaluated in order, so a value will be handled
     * by the first appropriate handler in the list.
     */
    'datatypes' => [
        App\Metable\DataType\SubmissionStatusHandler::class,

        Plank\Metable\DataType\BooleanHandler::class,
        Plank\Metable\DataType\NullHandler::class,
        Plank\Metable\DataType\IntegerHandler::class,
        Plank\Metable\DataType\FloatHandler::class,
        Plank\Metable\DataType\StringHandler::class,
        App\Metable\DataType\DateTimeHandler::class,
        Plank\Metable\DataType\ArrayHandler::class,
        Plank\Metable\DataType\ModelHandler::class,
        Plank\Metable\DataType\ModelCollectionHandler::class,
        Plank\Metable\DataType\SerializableHandler::class,
        Plank\Metable\DataType\ObjectHandler::class,
    ],

    'applyMigrations' => false,
];
