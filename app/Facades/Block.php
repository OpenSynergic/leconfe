<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void registerBlocks(array $blocks)
 */
class Block extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'block';
    }
}
