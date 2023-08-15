<?php

namespace App\Http\Middleware\Website;

use App\Models\Conference;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class ApplyCurrentConference
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        View::share('currentConference', Conference::current());

        // $currentConference = Conference::current();
        // dd($currentConference->navigations()->firstWhere('handle', 'user-navigation-menu')->items);


        return $next($request);
    }
}
