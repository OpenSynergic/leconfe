<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void registerBlocks(array $blocks)
 * @method static \Illuminate\Support\Collection getBlocks(string $position, bool $includeInactive = false)
 */
class Block extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'block';
    }
}
