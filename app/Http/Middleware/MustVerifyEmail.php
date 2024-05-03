<?php

namespace App\Http\Middleware;

use App\Facades\Settings;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MustVerifyEmail
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Settings::get('must_verify_email')) {
            return $next($request);
        }

        if (! $request->user()) {
            return redirect()->route('livewirePageGroup.website.pages.login');
        }

        if (! $request->user()->hasVerifiedEmail()) {
            return redirect()->route('livewirePageGroup.website.pages.email-verification');
        }

        return $next($request);
    }
}
