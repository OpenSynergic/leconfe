<?php

namespace App\Models\Enums\Concern;

trait UsefulEnums
{
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function array(): array
    {
        return array_combine(self::names(), self::values());
    }

    public static function random(): static
    {
        return self::from(self::values()[array_rand(self::values())]);
    }
}
