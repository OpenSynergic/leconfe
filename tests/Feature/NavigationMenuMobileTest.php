<?php

namespace Tests\Feature;

use App\Models\Conference;
use App\Models\NavigationMenu;
use App\Models\NavigationMenuItem;
use App\Models\ScheduledConference;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class NavigationMenuMobileTest extends TestCase
{
    use RefreshDatabase;

    public function test_dropdown_children_render_with_their_own_urls_for_primary_and_user_navigation(): void
    {
        $conference = Conference::query()->create([
            'name' => 'Navigation Test Conference',
            'path' => 'navigation-test-conference',
        ]);
        $scheduledConference = ScheduledConference::query()->create([
            'conference_id' => $conference->getKey(),
            'title' => '2026',
            'path' => '2026',
        ]);

        app()->setCurrentConferenceId($conference->getKey());
        app()->setCurrentScheduledConferenceId($scheduledConference->getKey());

        $this->addDropdownItem(
            'primary-navigation-menu',
            'Primary parent',
            'https://example.test/primary-parent',
            'Primary child',
            'https://example.test/primary-child',
        );
        $this->addDropdownItem(
            'user-navigation-menu',
            'User parent',
            'https://example.test/user-parent',
            'User child',
            'https://example.test/user-child',
        );

        $html = Blade::render(
            '<x-website::navigation-menu-mobile :headerLogo="$headerLogo" />',
            ['headerLogo' => null],
        );

        $this->assertStringContainsString('href="https://example.test/primary-child"', $html);
        $this->assertStringNotContainsString('href="https://example.test/primary-parent"', $html);
        $this->assertStringContainsString('href="https://example.test/user-child"', $html);
        $this->assertStringNotContainsString('href="https://example.test/user-parent"', $html);
    }

    private function addDropdownItem(
        string $handle,
        string $parentLabel,
        string $parentUrl,
        string $childLabel,
        string $childUrl,
    ): void {
        $menu = NavigationMenu::query()
            ->where('handle', $handle)
            ->firstOrFail();
        $parent = NavigationMenuItem::query()->forceCreate([
            'navigation_menu_id' => $menu->getKey(),
            'label' => $parentLabel,
            'type' => 'remote-url',
            'order_column' => 1,
        ]);
        $parent->setMeta('url', $parentUrl);

        $child = NavigationMenuItem::query()->forceCreate([
            'navigation_menu_id' => $menu->getKey(),
            'parent_id' => $parent->getKey(),
            'label' => $childLabel,
            'type' => 'remote-url',
            'order_column' => 1,
        ]);
        $child->setMeta('url', $childUrl);
    }
}
