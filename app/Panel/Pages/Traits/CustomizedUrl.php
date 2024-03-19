<?php

namespace App\Panel\Pages\Traits;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;

trait CustomizedUrl
{
    public static function getUrl(array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?Model $tenant = null): string
    {
        if (blank($panel) || Filament::getPanel($panel)->hasTenancy()) {
            $parameters['tenant'] ??= ($tenant ?? Filament::getTenant());
        }

        $parameters['conference'] ??= app()->getCurrentConference()->path;

        return route(static::getRouteName($panel), $parameters, $isAbsolute);
    }
}
