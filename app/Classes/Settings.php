<?php

namespace App\Classes;

use App\Models\Conference;

class Settings
{
    protected static $parsed_url;
    protected static $path_segments;
    protected static $getConference;

    public function __construct()
    {
        self::$parsed_url = parse_url(url()->current());
        self::$path_segments = explode('/', self::$parsed_url['path']);
        self::$path_segments = array_values(array_filter(self::$path_segments));
        self::$getConference = Conference::find(app()->getCurrentConferenceId());
    }

    public static function get($key = null)
    {
        if ($key !== null) {
            if (self::$getConference === null || (self::$getConference->hasMeta('settings.' . $key) && app()->getCurrentConference()->getOriginal('path') === self::$path_segments[0])) {
                return [
                    $key => self::$getConference ? self::$getConference->getMeta('settings.' . $key) : app()->getSite()->getMeta('settings.' . $key)
                ];
            }
            return [
                $key => app()->getSite()->getMeta('settings.' . $key)
            ];
        }
        return [
            $key => app()->getSite()->getAllMeta()->toArray()
        ];
    }
}
