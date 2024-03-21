<?php

namespace App\Panel\Conference\Pages\Traits;

use Illuminate\Database\Eloquent\Model;

trait CustomizedUrl
{
    public static function getUrl(array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?Model $tenant = null): string
    {
        if($conference = app()->getCurrentConference()){
            $parameters['conference'] ??= $conference->path;
        }

        return parent::getUrl($parameters, $isAbsolute, $panel, $tenant);
    }
}
