<?php

namespace App\Models\Constants;

class SidebarPosition
{
    const Left = 'left';
    const Right = 'right';
    const Both = 'both';
    const None = "none";

    public static function isRight()
    {
        return setting('sidebar') == static::Right || setting('sidebar') == static::Both;
    }

    public static function isLeft()
    {
        return setting('sidebar') == static::Left || setting('sidebar') == static::Both;
    }

    public static function isNone()
    {
        return setting('sidebar') == static::None;
    }
}
