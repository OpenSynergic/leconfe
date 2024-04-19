<?php

namespace App\Frontend\Website\Pages;

use App\Classes\CustomBlock;
use App\Facades\Block as BlockFacade;
use App\Models\Conference;
use App\Models\Topic;
use Illuminate\Support\Facades\Route;
use Rahmanramsi\LivewirePageGroup\PageGroup;
use Rahmanramsi\LivewirePageGroup\Pages\Page;

class Home extends Page
{
    protected static string $view = 'frontend.website.pages.home';

    public function mount()
    {
        BlockFacade::registerBlocks([
            new CustomBlock('test5', 'test5', 'test5 content'),
            new CustomBlock('test6', 'test6', 'test6 content'),
            new CustomBlock('test3', 'test3', 'test3 content'),
            new CustomBlock('test4', 'test4', 'test4 content'),
            new CustomBlock('test', 'test', 'test content'),
            new CustomBlock('test2', 'test2', 'test2 content'),
        ]);
        
        // $listBlocks = BlockFacade::getBlocks(false);
        
        // dd($listBlocks->map(fn($block) => $block->getName())->toArray());

        // BlockFacade::updateActiveBlockList($listBlocks->map(fn($block) => $block->getName())->toArray());

        // dd($listBlocks);
    }

    protected function getViewData(): array
    {
        return [
            // 'topics' => Topic::withoutGlobalScopes()->where('conference_id', $activeConference->getKey())->get(),
            'upcomingConferences' => Conference::upcoming()->get(),
            // 'activeConference' => $activeConference,
        ];
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
