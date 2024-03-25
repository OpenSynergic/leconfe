<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use Symfony\Component\HttpFoundation\Response;

class IdentifyConference
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(!app()->getCurrentConferenceId()){
            return abort(404);
        }

        return $next($request);
    }
}
