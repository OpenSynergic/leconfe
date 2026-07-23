<?php

namespace App\Routing;

use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Illuminate\Routing\RouteUrlGenerator;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Str;

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
     * @throws RouteNotFoundException
     */
    public function route($name, $parameters = [], $absolute = true)
    {
        $route = $this->routes->getByName($name);

        /**
         * Handle the conference and serie parameters when the route needs them
         */
        if ($route) {
            if (Str::contains($route->uri(), '{conference}') && $conference = app()->getCurrentConference()) {
                $parameters['conference'] ??= $conference->path;
            }

            if (Str::contains($route->uri(), '{serie}') && $scheduledConference = app()->getCurrentScheduledConference()) {
                $parameters['serie'] ??= $scheduledConference->path;
            }
        }

        return parent::route($name, $parameters, $absolute);
    }

    /**
     * Get the Route URL generator instance.
     *
     * @return RouteUrlGenerator
     */
    protected function routeUrl()
    {
        if (! $this->routeGenerator) {
            $this->routeGenerator = new CustomRouteUrlGenerator($this, $this->request);
        }

        return $this->routeGenerator;
    }
}
