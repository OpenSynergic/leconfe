<?php

namespace App\Panel\Series\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Database\Eloquent\Model;

class Dashboard extends BaseDashboard
{
    public static function getUrl(array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?Model $tenant = null): string
    {
        if ($conference = app()->getCurrentConference()) {
            $parameters['conference'] = $conference;
            $parameters['serie'] = 'dsadas';
        }

        return parent::getUrl($parameters, $isAbsolute, $panel, $tenant);
    }
}
