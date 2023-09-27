<?php

namespace App\Website\Pages;

use App\Mail\Templates\VerifyUserEmail;
use App\Models\Conference;
use App\Models\MailTemplate;
use App\Models\Topic;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Rahmanramsi\LivewirePageGroup\PageGroup;
use Rahmanramsi\LivewirePageGroup\Pages\Page;

class Home extends Page
{
    protected static string $view = 'website.pages.home';

    protected function getViewData(): array
    {
        return [
            'topics' => Topic::where('conference_id', Conference::active()->getKey())->get(),
            'upcomings' => Conference::upcoming()->get(),
            'activeConference' => Conference::active(),
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
