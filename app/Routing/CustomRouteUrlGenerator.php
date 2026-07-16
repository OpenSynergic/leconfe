<?php

namespace App\Routing;

use Illuminate\Routing\Route;
use Illuminate\Routing\Exceptions\UrlGenerationException;
use Illuminate\Routing\RouteUrlGenerator;

class CustomRouteUrlGenerator extends RouteUrlGenerator
{
    /**
     * Generate a URL for the given route.
     *
     * @param Route $route
     * @param  array  $parameters
     * @param  bool  $absolute
     * @return string
     *
     * @throws UrlGenerationException
     */
    public function to($route, $parameters = [], $absolute = false)
    {
        $parameters = $this->formatParameters($route, $parameters);

        $domain = $this->getRouteDomain($route, $parameters);

        // First we will construct the entire URI including the root and query string. Once it
        // has been constructed, we'll make sure we don't have any missing parameters or we
        // will need to throw the exception to let the developers know one was not given.
        $uri = $this->addQueryString($this->url->format(
            $root = $this->replaceRootParameters($route, $domain, $parameters),
            $this->replaceRouteParameters($route->uri(), $parameters),
            $route
        ), $parameters);

        $uriWithoutQuery = explode('?', $uri, 2)[0];

        if (preg_match_all('/{(.*?)}/', $uriWithoutQuery, $matchedMissingParameters)) {
            throw UrlGenerationException::forMissingParameters($route, $matchedMissingParameters[1]);
        }

        // Once we have ensured that there are no missing parameters in the URI we will encode
        // the URI and prepare it for returning to the developer. If the URI is supposed to
        // be absolute, we will return it as-is. Otherwise we will remove the URL's root.
        $uri = strtr(rawurlencode($uri), $this->dontEncode);

        if (! $absolute) {
            $uri = preg_replace('#^(//|[^/?])+#', '', $uri);

            return '/'.ltrim($uri, '/');
        }

        return $uri;
    }
}
