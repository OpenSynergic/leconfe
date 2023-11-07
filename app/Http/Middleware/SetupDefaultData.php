<?php

namespace App\Http\Middleware;

use Closure;
use App\Facades\MetaTag;
use App\Models\Conference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Models\Enums\ConferenceStatus;
use Symfony\Component\HttpFoundation\Response;

class SetupDefaultData
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($currentConference = app()->getCurrentConference()) {
            $this->setupConference($request, $currentConference);
        } else {
            $this->setupSite();
        }

        View::share('homeUrl', match ($currentConference?->status) {
            ConferenceStatus::Active => route('livewirePageGroup.current-conference.pages.home'),
            ConferenceStatus::Archived => route('livewirePageGroup.archive-conference.pages.home', ['conference' => $currentConference->path]),
            default => route('livewirePageGroup.website.pages.home'),
        });

        return $next($request);
    }

    protected function setupSite()
    {
        $site = app()->getSite();
        View::share('headerLogo', $site->getFirstMedia('logo')?->getAvailableUrl(['thumb', 'thumb-xl']));
        View::share('headerLogoAltText', $site->getMeta('name'));
        View::share('contextName', $site->getMeta('name'));
        View::share('footer', $site->getMeta('page_footer'));

        MetaTag::add('description', $site->getMeta('description') ?? 'dsadsa');
    }

    protected function setupConference(Request $request, $currentConference)
    {
        $previousConference = Conference::where('path', $request->route()->parameter('conference'))->first();

        View::share('headerLogoAltText', $request->route()->hasParameter('conference') ? $previousConference?->name : $currentConference?->name);
        View::share('headerLogo', $request->route()->hasParameter('conference') ? $previousConference->getFirstMedia('logo')?->getAvailableUrl(['thumb', 'thumb-xl'])
            : $currentConference->getFirstMedia('logo')?->getAvailableUrl(['thumb', 'thumb-xl']));
        View::share('currentConference', $currentConference);
        View::share('contextName', $currentConference->name);
        View::share('footer', $currentConference->getMeta('page_footer'));
        View::share('favicon', $currentConference->getFirstMediaUrl('favicon'));
        View::share('styleSheet', $currentConference->getFirstMediaUrl('styleSheet'));
        MetaTag::add('description', preg_replace("/\r|\n/", '', $currentConference->getMeta('description')));
    }
}
