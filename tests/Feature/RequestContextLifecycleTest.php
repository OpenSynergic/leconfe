<?php

namespace Tests\Feature;

use App\Frontend\ScheduledConference\Pages\Login;
use App\Http\Kernel;
use App\Http\Middleware\DetectConferenceContext;
use App\Http\Middleware\SetupDefaultData;
use App\Models\Conference;
use App\Models\NavigationMenu;
use App\Models\ScheduledConference;
use App\Models\Scopes\ConferenceScope;
use App\Models\Scopes\ScheduledConferenceScope;
use App\Models\Site;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
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

    public function test_scheduled_scope_only_constrains_queries_with_scheduled_context(): void
    {
        $scope = new ScheduledConferenceScope;

        app()->setCurrentScheduledConferenceId(null);
        $websiteQuery = ScheduledConference::query()->withoutGlobalScopes();
        $scope->apply($websiteQuery, new ScheduledConference);

        app()->setCurrentScheduledConferenceId(30);
        $scheduledQuery = ScheduledConference::query()->withoutGlobalScopes();
        $scope->apply($scheduledQuery, new ScheduledConference);

        $this->assertStringNotContainsString('scheduled_conference_id', $websiteQuery->toSql());
        $this->assertStringContainsString('scheduled_conference_id', $scheduledQuery->toSql());
        $this->assertContains(30, $scheduledQuery->getBindings());
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
        $livewireRoutes = collect(Route::getRoutes()->getRoutes())
            ->filter(fn ($route) => str($route->getName())->endsWith('livewire.update'));

        $this->assertContains(DetectConferenceContext::class, $persistentMiddleware);
        $this->assertNotContains(
            DetectConferenceContext::class,
            app('router')->gatherRouteMiddleware($scheduledLoginRoute),
        );
        $this->assertSame(EndpointResolver::updatePath(), app(HandleRequests::class)->getUpdateUri());
        $this->assertCount(1, $livewireRoutes);
        $this->assertSame('default-livewire.update', $livewireRoutes->first()->getName());
    }
}
