<?php

namespace Tests\Feature;

use App\Frontend\ScheduledConference\Pages\Login;
use App\Http\Kernel;
use App\Http\Middleware\DetectConferenceContext;
use App\Http\Middleware\IdentifyConference;
use App\Http\Middleware\IdentifyScheduledConference;
use App\Http\Middleware\RestoreLivewireConferenceContext;
use App\Http\Middleware\SetupDefaultData;
use App\Models\Conference;
use App\Models\Enums\UserRole;
use App\Models\NavigationMenu;
use App\Models\Role;
use App\Models\ScheduledConference;
use App\Models\Scopes\ConferenceScope;
use App\Models\Scopes\ScheduledConferenceScope;
use App\Models\Site;
use App\Models\User;
use App\Panel\ScheduledConference\Pages\Dashboard;
use Filament\Facades\Filament;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Laravel\Octane\CurrentApplication;
use Livewire\Mechanisms\HandleRequests\EndpointResolver;
use Livewire\Mechanisms\HandleRequests\HandleRequests;
use Livewire\Mechanisms\PersistentMiddleware\PersistentMiddleware;
use ReflectionClass;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

class RequestContextLifecycleTest extends TestCase
{
    use RefreshDatabase;

    public function test_sequential_tenant_then_website_request_clears_all_context(): void
    {
        $conference = Conference::query()->create([
            'name' => 'Conference A',
            'path' => 'conference-a',
        ]);

        $scheduledConference = ScheduledConference::query()->create([
            'conference_id' => $conference->getKey(),
            'title' => 'Scheduled A',
            'path' => 'scheduled-a',
        ]);
        Site::query()->create();

        $middleware = app(DetectConferenceContext::class);

        $middleware->handle(Request::create('/conference-a/scheduled/scheduled-a'), fn () => response('ok'));

        $this->assertSame($conference->getKey(), app()->getCurrentConferenceId());
        $this->assertSame($conference->getKey(), app()->getCurrentConference()?->getKey());
        $this->assertSame($scheduledConference->getKey(), app()->getCurrentScheduledConferenceId());
        $this->assertSame($scheduledConference->getKey(), app()->getCurrentScheduledConference()?->getKey());
        $tenantSite = app()->getSite();

        $middleware->handle(Request::create('/'), fn () => response('ok'));

        $this->assertSame(0, app()->getCurrentConferenceId());
        $this->assertNull(app()->getCurrentConference());
        $this->assertNull(app()->getCurrentScheduledConferenceId());
        $this->assertNull(app()->getCurrentScheduledConference());
        $this->assertNotSame($tenantSite, app()->getSite());
    }

    public function test_sequential_conference_requests_refresh_the_cached_model(): void
    {
        $conferenceA = Conference::query()->create([
            'name' => 'Conference A',
            'path' => 'conference-a',
        ]);
        $conferenceB = Conference::query()->create([
            'name' => 'Conference B',
            'path' => 'conference-b',
        ]);

        $middleware = app(DetectConferenceContext::class);
        $middleware->handle(Request::create('/conference-a'), fn () => response('ok'));
        $cachedConferenceA = app()->getCurrentConference();

        $middleware->handle(Request::create('/conference-b'), fn () => response('ok'));

        $this->assertSame($conferenceB->getKey(), app()->getCurrentConferenceId());
        $this->assertSame($conferenceB->getKey(), app()->getCurrentConference()?->getKey());
        $this->assertNotSame($cachedConferenceA, app()->getCurrentConference());
        $this->assertNotSame($conferenceA->getKey(), app()->getCurrentConference()?->getKey());
    }

    public function test_scheduled_then_conference_request_clears_scheduled_context(): void
    {
        $conference = Conference::query()->create([
            'name' => 'Conference A',
            'path' => 'conference-a',
        ]);
        ScheduledConference::query()->create([
            'conference_id' => $conference->getKey(),
            'title' => 'Scheduled A',
            'path' => 'scheduled-a',
        ]);

        $middleware = app(DetectConferenceContext::class);
        $middleware->handle(Request::create('/conference-a/scheduled/scheduled-a'), fn () => response('ok'));
        $this->assertNotNull(app()->getCurrentScheduledConference());

        $middleware->handle(Request::create('/conference-a'), fn () => response('ok'));

        $this->assertSame($conference->getKey(), app()->getCurrentConferenceId());
        $this->assertNull(app()->getCurrentScheduledConferenceId());
        $this->assertNull(app()->getCurrentScheduledConference());
    }

    public function test_invalid_scheduled_path_returns_not_found(): void
    {
        Conference::query()->create([
            'name' => 'Conference A',
            'path' => 'conference-a',
        ]);

        $this->expectException(NotFoundHttpException::class);

        app(DetectConferenceContext::class)->handle(
            Request::create('/conference-a/scheduled/missing'),
            fn () => response('ok'),
        );
    }

    public function test_scopes_read_the_current_context_when_the_query_is_built(): void
    {
        $conferenceScope = new ConferenceScope;

        app()->setCurrentConferenceId(10);
        $conferenceAQuery = ScheduledConference::query()->withoutGlobalScopes();
        $conferenceScope->apply($conferenceAQuery, new ScheduledConference);

        app()->setCurrentConferenceId(20);
        $conferenceBQuery = ScheduledConference::query()->withoutGlobalScopes();
        $conferenceScope->apply($conferenceBQuery, new ScheduledConference);

        $this->assertContains(10, $conferenceAQuery->getBindings());
        $this->assertContains(20, $conferenceBQuery->getBindings());
    }

    public function test_scheduled_scope_hides_scheduled_rows_from_conference_context(): void
    {
        $conference = Conference::query()->create([
            'name' => 'Conference A',
            'path' => 'conference-a',
        ]);
        $scheduledConferenceA = ScheduledConference::query()->create([
            'conference_id' => $conference->getKey(),
            'title' => 'Scheduled A',
            'path' => 'scheduled-a',
        ]);
        $scheduledConferenceB = ScheduledConference::query()->create([
            'conference_id' => $conference->getKey(),
            'title' => 'Scheduled B',
            'path' => 'scheduled-b',
        ]);
        $conferenceMenu = NavigationMenu::query()->withoutGlobalScopes()->create([
            'conference_id' => $conference->getKey(),
            'scheduled_conference_id' => 0,
            'name' => 'Conference menu',
            'handle' => 'conference-menu',
        ]);
        $scheduledConferenceAMenu = NavigationMenu::query()->withoutGlobalScopes()->create([
            'conference_id' => $conference->getKey(),
            'scheduled_conference_id' => $scheduledConferenceA->getKey(),
            'name' => 'Scheduled A menu',
            'handle' => 'scheduled-a-menu',
        ]);
        $scheduledConferenceBMenu = NavigationMenu::query()->withoutGlobalScopes()->create([
            'conference_id' => $conference->getKey(),
            'scheduled_conference_id' => $scheduledConferenceB->getKey(),
            'name' => 'Scheduled B menu',
            'handle' => 'scheduled-b-menu',
        ]);

        app()->setCurrentScheduledConferenceId(null);
        $visibleMenuIds = NavigationMenu::query()
            ->withoutGlobalScopes()
            ->withGlobalScope('scheduled-conference', new ScheduledConferenceScope)
            ->whereKey([
                $conferenceMenu->getKey(),
                $scheduledConferenceAMenu->getKey(),
                $scheduledConferenceBMenu->getKey(),
            ])
            ->pluck('id');

        $this->assertSame([$conferenceMenu->getKey()], $visibleMenuIds->all());
    }

    public function test_detector_runs_before_context_consuming_global_middleware(): void
    {
        $kernel = app(Kernel::class);
        $property = (new ReflectionClass($kernel))->getProperty('middleware');
        $middleware = $property->getValue($kernel);

        $this->assertContains(DetectConferenceContext::class, $middleware);
        $this->assertLessThan(
            array_search(SetupDefaultData::class, $middleware, true),
            array_search(DetectConferenceContext::class, $middleware, true),
        );
    }

    public function test_console_boot_does_not_register_tenant_scopes(): void
    {
        $conferenceScopes = collect((new ScheduledConference)->getGlobalScopes());
        $scheduledConferenceScopes = collect((new NavigationMenu)->getGlobalScopes());

        $this->assertFalse($conferenceScopes->contains(fn ($scope) => $scope instanceof ConferenceScope));
        $this->assertFalse($scheduledConferenceScopes->contains(fn ($scope) => $scope instanceof ScheduledConferenceScope));
    }

    public function test_spatie_permission_resets_cached_state_for_octane_requests(): void
    {
        $this->assertTrue(config('permission.register_octane_reset_listener'));
    }

    public function test_livewire_keeps_the_default_endpoint_and_persists_context_detection(): void
    {
        $persistentMiddleware = app(PersistentMiddleware::class)->getPersistentMiddleware();
        $scheduledLoginRoute = Route::getRoutes()->getByName(Login::getRouteName('scheduledConference'));
        $conferenceDashboardRoute = Route::getRoutes()->getByName('filament.conference.pages.dashboard');
        $scheduledDashboardRoute = Route::getRoutes()->getByName('filament.scheduledConference.pages.dashboard');
        $livewireRoutes = collect(Route::getRoutes()->getRoutes())
            ->filter(fn ($route) => str($route->getName())->endsWith('livewire.update'));

        $this->assertContains(RestoreLivewireConferenceContext::class, $persistentMiddleware);
        $this->assertNotContains(DetectConferenceContext::class, $persistentMiddleware);
        $this->assertNotContains(
            DetectConferenceContext::class,
            app('router')->gatherRouteMiddleware($scheduledLoginRoute),
        );
        $this->assertContains(
            RestoreLivewireConferenceContext::class,
            app('router')->gatherRouteMiddleware($scheduledLoginRoute),
        );
        $conferenceDashboardMiddleware = app('router')->gatherRouteMiddleware($conferenceDashboardRoute);
        $scheduledDashboardMiddleware = app('router')->gatherRouteMiddleware($scheduledDashboardRoute);
        $this->assertLessThan(
            array_search(IdentifyConference::class, $conferenceDashboardMiddleware, true),
            array_search(RestoreLivewireConferenceContext::class, $conferenceDashboardMiddleware, true),
        );
        $this->assertLessThan(
            array_search(IdentifyScheduledConference::class, $scheduledDashboardMiddleware, true),
            array_search(RestoreLivewireConferenceContext::class, $scheduledDashboardMiddleware, true),
        );
        $this->assertSame(EndpointResolver::updatePath(), app(HandleRequests::class)->getUpdateUri());
        $this->assertCount(1, $livewireRoutes);
        $this->assertSame('default-livewire.update', $livewireRoutes->first()->getName());
    }

    public function test_scheduled_login_handles_the_rendered_livewire_update_endpoint(): void
    {
        $conference = Conference::query()->create([
            'name' => 'Conference A',
            'path' => 'conference-a',
        ]);
        $scheduledConference = ScheduledConference::query()->create([
            'conference_id' => $conference->getKey(),
            'title' => 'Scheduled A',
            'path' => 'scheduled-a',
        ]);

        $page = $this->withoutVite()->get(route(Login::getRouteName('scheduledConference'), [
            'conference' => $conference->path,
            'serie' => $scheduledConference->path,
        ]))->assertOk();

        preg_match('/wire:snapshot="([^"]+)"/', $page->getContent(), $matches);
        $snapshot = html_entity_decode($matches[1], ENT_QUOTES | ENT_HTML5);

        $this->postJson(EndpointResolver::updatePath(), [
            'components' => [[
                'snapshot' => $snapshot,
                'updates' => [
                    'email' => 'invalid@example.test',
                    'password' => 'invalid-password',
                ],
                'calls' => [[
                    'method' => 'login',
                    'params' => [],
                    'metadata' => [],
                ]],
            ]],
        ], ['X-Livewire' => '1'])
            ->assertOk();

        $this->assertSame($conference->getKey(), app()->getCurrentConferenceId());
        $this->assertSame($scheduledConference->getKey(), app()->getCurrentScheduledConferenceId());
    }

    public function test_scheduled_panel_handles_the_rendered_livewire_update_endpoint(): void
    {
        $conference = Conference::query()->create([
            'name' => 'Conference A',
            'path' => 'conference-a',
        ]);
        $scheduledConference = ScheduledConference::query()->create([
            'conference_id' => $conference->getKey(),
            'title' => 'Scheduled A',
            'path' => 'scheduled-a',
        ]);
        Role::withoutGlobalScopes()->create([
            'name' => UserRole::Admin->value,
            'guard_name' => 'web',
            'conference_id' => 0,
            'scheduled_conference_id' => 0,
        ]);
        $admin = User::factory()->create([
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole(UserRole::Admin->value);

        $page = $this->actingAs($admin)->withoutVite()->get(route(Dashboard::getRouteName(Filament::getPanel('scheduledConference')), [
            'conference' => $conference->path,
            'serie' => $scheduledConference->path,
        ]))->assertOk();

        preg_match_all('/wire:snapshot="([^"]+)"/', $page->getContent(), $matches);
        $snapshot = collect($matches[1])
            ->map(fn (string $snapshot) => html_entity_decode($snapshot, ENT_QUOTES | ENT_HTML5))
            ->first(fn (string $snapshot) => data_get(json_decode($snapshot, true), 'memo.name') === Dashboard::class);

        $this->assertNotNull($snapshot);

        $this->postJson(EndpointResolver::updatePath(), [
            'components' => [[
                'snapshot' => $snapshot,
                'updates' => [],
                'calls' => [],
            ]],
        ], ['X-Livewire' => '1'])
            ->assertOk();

        $this->assertSame($conference->getKey(), app()->getCurrentConferenceId());
        $this->assertSame($scheduledConference->getKey(), app()->getCurrentScheduledConferenceId());
    }

    public function test_octane_gate_resolves_the_authenticated_user_from_the_request_sandbox(): void
    {
        $rootApplication = app();
        $sandbox = clone $rootApplication;
        $user = new User;
        $sandbox->instance('auth', new class($user) implements AuthFactory
        {
            public function __construct(private User $user) {}

            public function guard($name = null)
            {
                throw new \LogicException('The gate resolver should use the current authenticated user.');
            }

            public function shouldUse($name): void {}

            public function user(): User
            {
                return $this->user;
            }
        });

        $gate = $rootApplication->make(GateContract::class);
        $gate->define('resolve-octane-sandbox-user', fn (User $resolvedUser): bool => $resolvedUser === $user);
        $gate->setContainer($sandbox);
        CurrentApplication::set($sandbox);

        try {
            $this->assertTrue($gate->allows('resolve-octane-sandbox-user'));
        } finally {
            $gate->setContainer($rootApplication);
            CurrentApplication::set($rootApplication);
        }
    }
}
