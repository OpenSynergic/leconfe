<?php

namespace App\Website\Pages;

use App\Models\Topic;
use App\Models\Announcement;
use App\Models\Conference;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Route;
use Rahmanramsi\LivewirePageGroup\PageGroup;
use Rahmanramsi\LivewirePageGroup\Pages\Page;

class Home extends Page
{
    protected static string $view = 'website.pages.home';


    protected function getViewData(): array
    {
        return [
            'announcements' => Announcement::where('conference_id', Conference::current()->getKey())->get(),
            'topics' => Topic::where('conference_id', Conference::current()->getKey())->get(),
            'upcomings' => Conference::upcoming(),
        ];
    }
    public function mount()
    {
    }

    public static function routes(PageGroup $pageGroup): void
    {
        $slug = static::getSlug();
        Route::get('/', static::class)
            ->middleware(static::getRouteMiddleware($pageGroup))
            ->withoutMiddleware(static::getWithoutRouteMiddleware($pageGroup))
            ->name((string) str($slug)->replace('/', '.'));
    }
}
