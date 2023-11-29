<?php

namespace App\Http\Middleware;

use App\Facades\MetaTag;
use App\Models\Conference;
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
            $this->setupConference($request, $currentConference);
        } else {
            $this->setupSite();
        }

        View::share('homeUrl', match ($currentConference?->status) {
            ConferenceStatus::Active => route('livewirePageGroup.current-conference.pages.home'),
            ConferenceStatus::Archived => route('livewirePageGroup.archive-conference.pages.home', ['conference' => $currentConference->path]),
            default => route('livewirePageGroup.website.pages.home'),
        });
        
        View::share('panelUrl', match ($currentConference instanceof Conference) {
            true => route('filament.panel.pages.dashboard', $currentConference?->path),
            default => route('filament.panel.tenant'),
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
        View::share('favicon', $site->getFirstMediaUrl('favicon'));
        View::share('styleSheet', $site->getFirstMediaUrl('styleSheet'));


        MetaTag::add('description', $site->getMeta('description'));
    }

    protected function setupConference(Request $request, $currentConference)
    {
        View::share('headerLogo', $currentConference->getFirstMedia('logo')?->getAvailableUrl(['thumb', 'thumb-xl']));
        View::share('headerLogoAltText', $currentConference->name);
        View::share('currentConference', $currentConference);
        View::share('contextName', $currentConference->name);
        View::share('footer', $currentConference->getMeta('page_footer'));
        View::share('favicon', $currentConference->getFirstMediaUrl('favicon'));
        View::share('styleSheet', $currentConference->getFirstMediaUrl('styleSheet'));

        MetaTag::add('description', preg_replace("/\r|\n/", '', $currentConference->getMeta('description')));

        foreach ($currentConference->getMeta('meta_tags') ?? [] as $name => $content) {
            MetaTag::add($name, $content);
        }
    }
}
