<?php

namespace App\Panel\Conference\Resources\Traits;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;

trait CustomizedUrl
{
    public static function getUrl(string $name = 'index', array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?Model $tenant = null): string
    {
        if($conference = app()->getCurrentConference()){
            $parameters['conference'] ??= $conference->path;
        }

        return parent::getUrl($name, $parameters, $isAbsolute, $panel, $tenant);
    }
}
