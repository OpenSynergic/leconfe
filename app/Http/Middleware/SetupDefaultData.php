<?php

namespace App\Http\Middleware;

use App\Facades\MetaTag;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use luizbills\CSS_Generator\Generator as CSSGenerator;
use matthieumastadenis\couleur\ColorFactory;
use matthieumastadenis\couleur\ColorSpace;
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

        View::share('currentConference', $currentConference);
        View::share('homeUrl', $currentConference ? route('livewirePageGroup.conference.pages.home') : route('livewirePageGroup.website.pages.home'));

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

        if ($appearanceColor = $site->getMeta('appearance_color')) {
            $oklch = ColorFactory::new($appearanceColor)->to(ColorSpace::OkLch);
            $css = new CSSGenerator();
            $css->root_variable('p', "{$oklch->lightness}% {$oklch->chroma} {$oklch->hue}");

            View::share('appearanceColor', $css->get_output());
        }

        MetaTag::add('description', $site->getMeta('description'));
    }

    protected function setupConference(Request $request, $currentConference)
    {
        View::share('headerLogo', $currentConference->getFirstMedia('logo')?->getAvailableUrl(['thumb', 'thumb-xl']));
        View::share('headerLogoAltText', $currentConference->name);
        View::share('contextName', $currentConference->name);
        View::share('footer', $currentConference->getMeta('page_footer'));
        View::share('favicon', $currentConference->getFirstMediaUrl('favicon'));
        View::share('styleSheet', $currentConference->getFirstMediaUrl('styleSheet'));

        if ($appearanceColor = $currentConference->getMeta('appearance_color')) {
            $oklch = ColorFactory::new($appearanceColor)->to(ColorSpace::OkLch);
            $css = new CSSGenerator();
            $css->root_variable('p', "{$oklch->lightness}% {$oklch->chroma} {$oklch->hue}");

            View::share('appearanceColor', $css->get_output());
        }

        MetaTag::add('description', preg_replace("/\r|\n/", '', $currentConference->getMeta('description')));

        foreach ($currentConference->getMeta('meta_tags') ?? [] as $name => $content) {
            MetaTag::add($name, $content);
        }
    }
}
