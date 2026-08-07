<?php

namespace Tests\Feature;

use App\Frontend\Website\Pages\StaticPage as WebsiteStaticPage;
use App\Models\StaticPage as StaticPageModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaticPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_static_page_uses_created_title_for_browser_title(): void
    {
        $staticPage = StaticPageModel::withoutGlobalScopes()->create([
            'conference_id' => 0,
            'scheduled_conference_id' => 0,
            'title' => 'Research Ethics',
            'slug' => 'research-ethics',
        ]);

        $this->withoutVite()
            ->get(route(WebsiteStaticPage::getRouteName('website'), [
                'staticPage' => $staticPage->slug,
            ]))
            ->assertOk()
            ->assertSee('<title>Research Ethics -', false)
            ->assertDontSee('<title>Static Page -', false);
    }
}
