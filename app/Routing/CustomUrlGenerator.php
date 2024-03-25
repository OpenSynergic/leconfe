<?php

namespace App\Routing;

use Illuminate\Support\Str;
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
        $route = $this->routes->getByName($name);
        
        /**
         * Handle the conference and serie parameters when the route needs them
         */
        if($route) {
            if (Str::contains($route->uri(), '{conference}') && $conference = app()->getCurrentConference()) {
                $parameters['conference'] ??= $conference->path;
            }
            
            if(Str::contains($route->uri(), '{serie}') && $serie = app()->getCurrentSerie()) {
                $parameters['serie'] ??= $serie->path;
            }
        }


        return parent::route($name, $parameters, $absolute);
    }
}
