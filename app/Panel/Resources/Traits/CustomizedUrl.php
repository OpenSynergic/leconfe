<?php

namespace App\Panel\Resources\Traits;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;

trait CustomizedUrl
{
    public static function getUrl(string $name = 'index', array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?Model $tenant = null): string
    {
        if (blank($panel) || Filament::getPanel($panel)->hasTenancy()) {
            $parameters['tenant'] ??= ($tenant ?? Filament::getTenant());
        }

        $routeBaseName = static::getRouteBaseName(panel: $panel);
        $parameters['conference'] ??= app()->getCurrentConference()->path;

        return route("{$routeBaseName}.{$name}", $parameters, $isAbsolute);
    }
}
