<?php

namespace App\Http\Middleware;

use App\Facades\MetaTag;
use App\Models\Enums\ConferenceStatus;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
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
            $this->setupConference($currentConference);
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
        View::share('footer', $site->getMeta('footer'));

        MetaTag::add('description', $site->getMeta('description') ?? 'dsadsa');
    }

    protected function setupConference($currentConference)
    {
        View::share('currentConference', $currentConference);
        View::share('headerLogo', $currentConference->getFirstMedia('logo')?->getAvailableUrl(['thumb', 'thumb-xl']));
        View::share('headerLogoAltText', $currentConference->name);
        View::share('contextName', $currentConference->name);
        View::share('footer', $currentConference->getMeta('footer'));
        View::share('favicon', $currentConference->getFirstMedia('logo') ? $currentConference->getFirstMedia('logo')->getFullUrl() : null);
        $styleSheet = $currentConference->getFirstMedia('stylesheet');
        View::share('styleSheet', $styleSheet ? $styleSheet->getFullUrl() : null);
        MetaTag::add('description', preg_replace("/\r|\n/", '', $currentConference->getMeta('description')));
    }
}
