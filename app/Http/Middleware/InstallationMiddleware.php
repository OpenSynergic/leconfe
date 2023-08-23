<?php

namespace App\Http\Middleware;

use App\Website\Pages\Installation;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class InstallationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        
        // Allow livewire update route
        if (Route::getCurrentRoute()->uri === 'livewire/update') {
            return $next($request);
        }
        
        $installationPage = Installation::getSlug();


        if (!app()->isInstalled() && Route::getCurrentRoute()->uri !== $installationPage) {
            return redirect($installationPage);
        }


        return $next($request);
    }
}
