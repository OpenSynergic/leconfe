<?php

namespace App\Http\Middleware;

use App\Models\Enums\ConferenceStatus;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class DefaultViewData
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($currentConference = app()->getCurrentConference()) {
            View::share('currentConference', $currentConference);
            View::share('headerLogo', $currentConference->getFirstMedia('logo')?->getAvailableUrl(['thumb','thumb-xl']));
            View::share('headerLogoAltText', $currentConference->name);
            View::share('contextName', $currentConference->name);
            View::share('footer', $currentConference->getMeta('footer'));
        } else {
            $site = app()->getSite();
            View::share('headerLogo', $site->getFirstMedia('logo')?->getAvailableUrl(['thumb','thumb-xl']));
            View::share('headerLogoAltText', $site->getMeta('name'));
            View::share('contextName', $site->getMeta('name'));
            View::share('footer', $site->getMeta('footer'));
        }
        View::share('homeUrl', match($currentConference?->status){
            ConferenceStatus::Active => route('livewirePageGroup.current-conference.pages.home'),
            ConferenceStatus::Archived => route('livewirePageGroup.archive-conference.pages.home', ['conference' => $currentConference->path]),
            default => route('livewirePageGroup.website.pages.home'),
        });

        return $next($request);
    }
}
