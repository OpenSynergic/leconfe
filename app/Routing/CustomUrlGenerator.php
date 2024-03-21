<?php

namespace App\Routing;

use Illuminate\Routing\UrlGenerator;

class CustomUrlGenerator extends UrlGenerator
{
    /**
     * Get the URL to a named route.
     *
     * @param  string  $name
     * @param  mixed  $parameters
     * @param  bool  $absolute
     * @return string
     *
     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException
     */
    public function route($name, $parameters = [], $absolute = true)
    {
        if ($conference = app()->getCurrentConference()) {
            $parameters['conference'] ??= $conference->path;
        }

        return parent::route($name, $parameters, $absolute);
    }
}
