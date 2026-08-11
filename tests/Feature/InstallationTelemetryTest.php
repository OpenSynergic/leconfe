<?php

namespace Tests\Feature;

use App\Actions\Leconfe\CheckLatestVersion;
use App\Actions\Leconfe\SendTelemetry;
use App\Facades\Plugin;
use App\Facades\Setting;
use App\Http\Middleware\SendTelemetryAfterRequest;
use App\Models\Conference;
use App\Models\Participant;
use App\Models\PluginSetting;
use App\Models\ScheduledConference;
use App\Models\Site;
use App\Models\User;
use App\Models\Version;
use App\Services\Telemetry\TelemetrySettings;
use App\Services\Telemetry\TelemetrySnapshotBuilder;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request as HttpRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class InstallationTelemetryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('app.installed', true);
        Site::query()->create();
        Version::query()->create([
            'product_name' => 'Leconfe',
            'product_folder' => 'leconfe',
            'version' => '1.5.0',
        ]);
    }

    public function test_snapshot_is_exactly_the_v1_allowlist_and_counts_features_per_schedule(): void
    {
        $conference = Conference::query()->create([
            'name' => 'Conference One',
            'path' => 'conference-one',
        ]);
        $secondConference = Conference::query()->create([
            'name' => 'Conference Two',
            'path' => 'conference-two',
        ]);
        $firstSchedule = $this->scheduledConference($conference, 'schedule-one', [
            'allow_registration' => true,
            'submission_payment' => true,
            'participant_payment' => false,
            'invoice_enable' => true,
            'receipt_enable' => false,
            'review_mode' => 1,
        ]);
        $this->scheduledConference($secondConference, 'schedule-two', [
            'allow_registration' => false,
            'submission_payment' => false,
            'participant_payment' => true,
            'invoice_enable' => false,
            'receipt_enable' => true,
            'review_mode' => 2,
        ]);
        $setting = new PluginSetting([
            'conference_id' => $secondConference->getKey(),
            'scheduled_conference_id' => 0,
            'plugin' => 'ManualPayment',
            'key' => 'enabled',
            'value' => '0',
            'type' => 'boolean',
        ]);
        $setting->timestamps = false;
        $setting->save();
        User::query()->create([
            'given_name' => 'Admin',
            'family_name' => 'Example',
            'email' => 'sensitive-admin@example.test',
            'password' => Hash::make('password'),
        ]);
        Participant::query()->create([
            'conference_id' => $conference->getKey(),
            'scheduled_conference_id' => $firstSchedule->getKey(),
            'given_name' => 'Sensitive Participant Name',
            'family_name' => 'Sensitive Participant Family',
            'email' => 'sensitive-participant@example.test',
        ]);

        $payload = app(TelemetrySnapshotBuilder::class)->build();

        $this->assertSame([
            'snapshot_date' => now('UTC')->toDateString(),
            'schema_version' => 1,
            'environment' => [
                'leconfe_version' => '1.5.0',
                'php_version' => PHP_VERSION,
                'database_driver' => 'sqlite',
                'plugins' => [
                    ['name' => 'DefaultTheme', 'version' => '1.0.0'],
                    ['name' => 'ManualPayment', 'version' => '1.0.0'],
                ],
            ],
            'inventory' => [
                'conferences' => 2,
                'scheduled_conferences' => 2,
                'users' => 1,
            ],
            'core_workflow' => [
                'submissions_by_status' => [
                    'Incomplete' => 0,
                    'Queued' => 0,
                    'On Review' => 0,
                    'On Presentation' => 0,
                    'Editing' => 0,
                    'Published' => 0,
                    'Declined' => 0,
                    'Withdrawn' => 0,
                ],
                'reviews_assigned' => 0,
                'reviews_confirmed' => 0,
                'reviews_completed' => 0,
                'participants' => 1,
                'payments_total' => 0,
                'payments_paid' => 0,
                'payments_unpaid' => 0,
                'published_proceedings' => 0,
                'doi_count' => 0,
            ],
            'feature_adoption' => [
                'scheduled_conferences_using_registration' => 1,
                'scheduled_conferences_using_submission_payment' => 1,
                'scheduled_conferences_using_participant_payment' => 1,
                'scheduled_conferences_using_invoice' => 1,
                'scheduled_conferences_using_receipt' => 1,
                'scheduled_conferences_using_review_modes' => 2,
                'scheduled_conferences_using_presentations' => 0,
                'enabled_plugins' => [
                    ['name' => 'DefaultTheme', 'scheduled_conferences' => 2],
                    ['name' => 'ManualPayment', 'scheduled_conferences' => 1],
                ],
            ],
        ], $payload);

        $serialized = json_encode($payload, JSON_THROW_ON_ERROR);
        foreach ([
            'sensitive-admin@example.test',
            'Sensitive Participant Name',
            'sensitive-participant@example.test',
        ] as $prohibitedField) {
            $this->assertStringNotContainsString(strtolower($prohibitedField), strtolower($serialized));
        }

        $keys = [];
        array_walk_recursive($payload, function (mixed $value, int|string $key) use (&$keys): void {
            $keys[] = $key;
        });
        foreach (['title', 'email', 'amount', 'invoice', 'receipt', 'payment_method', 'review_author_editor', 'review_editor'] as $prohibitedKey) {
            $this->assertNotContains($prohibitedKey, $keys);
        }
    }

    public function test_registration_and_daily_snapshot_are_deduplicated_and_token_is_encrypted(): void
    {
        User::query()->create([
            'given_name' => 'Admin',
            'family_name' => 'Example',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);
        Http::fake([
            '*v1/installations/register' => Http::response([
                'installation_id' => 'server-installation-id',
                'token' => 'installation-token',
            ], 200),
            '*v1/installations/snapshots' => Http::response(['schema_version' => 1], 200),
        ]);

        $first = SendTelemetry::run();
        $second = SendTelemetry::run();

        $this->assertSame('sent', $first['status'], json_encode($first));
        $this->assertSame('skipped', $second['status']);
        Http::assertSentCount(2);
        $this->assertNotSame('installation-token', Setting::get(TelemetrySettings::INSTALLATION_TOKEN));
        $this->assertSame('installation-token', app(TelemetrySettings::class)->token());
        $this->assertSame('server-installation-id', app(TelemetrySettings::class)->installationId());
        $this->assertNotSame('server-installation-id', Setting::get(TelemetrySettings::INSTALLATION_ID));
        $this->assertSame('sent', Setting::get(TelemetrySettings::LAST_ATTEMPT_STATUS));
        $this->assertNotNull(Setting::get(TelemetrySettings::LAST_ATTEMPT_AT));

        $requests = Http::recorded();
        $this->assertSame('v1/installations/register', trim(parse_url($requests[0][0]->url(), PHP_URL_PATH), '/api/'));
        $this->assertArrayNotHasKey('snapshot_date', $requests[0][0]->data());
        $this->assertArrayNotHasKey('installation_id', $requests[1][0]->data());
        $this->assertFalse($requests[0][0]->hasHeader('Beacon'));
        $this->assertSame(now('UTC')->toDateString(), $requests[1][0]->data()['snapshot_date']);
    }

    public function test_opt_out_prevents_registration_and_snapshot_without_deleting_existing_state(): void
    {
        Setting::set(TelemetrySettings::ENABLED, false);
        Setting::set(TelemetrySettings::INSTALLATION_TOKEN, 'existing-encrypted-value');
        Http::fake();

        $result = SendTelemetry::run();

        $this->assertSame('disabled', $result['status']);
        Http::assertNothingSent();
        $this->assertSame('existing-encrypted-value', Setting::get(TelemetrySettings::INSTALLATION_TOKEN));
    }

    public function test_telemetry_opt_out_is_site_scoped_inside_a_conference_context(): void
    {
        $conference = Conference::query()->create([
            'name' => 'Conference Context',
            'path' => 'conference-context',
        ]);
        $conference->setMeta('settings_'.TelemetrySettings::ENABLED, true);
        app()->setCurrentConferenceId($conference->getKey());
        app(TelemetrySettings::class)->setEnabled(false);
        Http::fake();

        $this->assertFalse(app(TelemetrySettings::class)->enabled());
        $this->assertSame('disabled', SendTelemetry::run()['status']);
        $this->assertTrue((bool) $conference->getMeta('settings_'.TelemetrySettings::ENABLED));
        Http::assertNothingSent();
    }

    public function test_legacy_app_beacon_opt_out_remains_disabled(): void
    {
        config()->set('app.beacon', false);
        Http::fake();

        $this->assertFalse(app(TelemetrySettings::class)->enabled());
        $this->assertSame('disabled', SendTelemetry::run()['status']);
        Http::assertNothingSent();
    }

    public function test_upgrade_notice_does_not_delay_default_on_telemetry(): void
    {
        Setting::set(TelemetrySettings::UPGRADE_NOTICE_PENDING, true);
        Setting::set(TelemetrySettings::UPGRADE_NOTICE_SHOWN, false);
        Http::fake([
            '*v1/installations/register' => Http::response(['token' => 'installation-token'], 200),
            '*v1/installations/snapshots' => Http::response(['schema_version' => 1], 200),
        ]);

        $this->assertTrue(app(TelemetrySettings::class)->enabled());
        $this->assertSame('sent', SendTelemetry::run()['status']);
    }

    public function test_failed_send_is_non_blocking_and_visible(): void
    {
        Http::fake(function (): void {
            throw new ConnectionException('telemetry endpoint unavailable');
        });

        $result = SendTelemetry::run();

        $this->assertSame('failed', $result['status']);
        $this->assertSame('failed', Setting::get(TelemetrySettings::LAST_ATTEMPT_STATUS));
        $this->assertStringContainsString('unavailable', Setting::get(TelemetrySettings::LAST_ATTEMPT_ERROR));
        $this->assertSame(now('UTC')->toDateString(), Setting::get(TelemetrySettings::LAST_ATTEMPT_DATE));
    }

    public function test_failed_send_can_retry_same_day_and_success_is_still_deduplicated(): void
    {
        $requestCount = 0;
        Http::fake(function (HttpRequest $request) use (&$requestCount) {
            if ($requestCount++ === 0) {
                throw new ConnectionException('telemetry endpoint unavailable');
            }

            return str_contains($request->url(), '/register')
                ? Http::response(['token' => 'installation-token'], 200)
                : Http::response(['schema_version' => 1], 200);
        });

        $this->assertSame('failed', SendTelemetry::run()['status']);
        $this->assertSame('sent', SendTelemetry::run()['status']);
        $this->assertSame('skipped', SendTelemetry::run()['status']);
        Http::assertSentCount(2);
    }

    public function test_scheduler_and_authenticated_request_trigger_share_daily_dedupe_state(): void
    {
        config()->set('queue.default', 'sync');

        $admin = User::query()->create([
            'given_name' => 'Admin',
            'family_name' => 'Example',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);
        Http::fake([
            '*v1/installations/register' => Http::response(['token' => 'installation-token'], 200),
            '*v1/installations/snapshots' => Http::response(['schema_version' => 1], 200),
        ]);

        $schedule = new Schedule;
        $method = new \ReflectionMethod(\App\Console\Kernel::class, 'schedule');
        $method->setAccessible(true);
        $method->invoke(app(\App\Console\Kernel::class), $schedule);

        $telemetryEvent = collect($schedule->events())
            ->first(fn ($event): bool => $event->getSummaryForDisplay() === 'Send Leconfe installation telemetry');

        $this->assertNotNull($telemetryEvent);
        $telemetryEvent->run(app());

        $this->actingAs($admin);
        app(SendTelemetryAfterRequest::class)->terminate(
            Request::create('/panel'),
            new Response('', 200),
        );

        Http::assertSentCount(2);
        $this->assertSame('sent', Setting::get(TelemetrySettings::LAST_ATTEMPT_STATUS));
    }

    public function test_authenticated_requests_enqueue_only_one_daily_telemetry_job(): void
    {
        Cache::forget('leconfe-telemetry-dispatch-'.now('UTC')->toDateString());
        Queue::fake();
        $admin = User::query()->create([
            'given_name' => 'Admin',
            'family_name' => 'Example',
            'email' => 'queue-admin@example.com',
            'password' => Hash::make('password'),
        ]);
        $this->actingAs($admin);

        $middleware = app(SendTelemetryAfterRequest::class);
        $middleware->terminate(Request::create('/panel'), new Response('', 200));
        $middleware->terminate(Request::create('/panel'), new Response('', 200));

        SendTelemetry::assertPushed(1);
        Http::assertNothingSent();
    }

    public function test_telemetry_plugin_discovery_does_not_load_external_code(): void
    {
        Storage::fake('plugins');
        Storage::disk('plugins')->put('TargetedTelemetry/index.yaml', <<<'YAML'
folder: TargetedTelemetry
name: Targeted telemetry
version: 1.0.0
targets:
  - scheduled-conference
YAML);
        Storage::disk('plugins')->put('TargetedTelemetry/index.php', <<<'PHP'
<?php

throw new \RuntimeException('Telemetry must not include external plugin code.');
PHP);

        $defaultTheme = Plugin::getPlugin('DefaultTheme', false);
        $plugins = Plugin::getPluginsForTelemetry();
        $this->assertSame('TargetedTelemetry', $plugins->firstWhere('name', 'TargetedTelemetry')['name']);
        $this->assertSame($defaultTheme, Plugin::getPlugin('DefaultTheme', false));
    }

    public function test_plugin_adoption_uses_each_schedule_setting_not_request_context(): void
    {
        $conference = Conference::query()->create([
            'name' => 'Conference One',
            'path' => 'conference-one',
        ]);
        $secondConference = Conference::query()->create([
            'name' => 'Conference Two',
            'path' => 'conference-two',
        ]);
        $firstSchedule = $this->scheduledConference($conference, 'schedule-one', []);
        $this->scheduledConference($secondConference, 'schedule-two', []);

        Storage::fake('plugins');
        Storage::disk('plugins')->put('TargetedTelemetry/index.yaml', <<<'YAML'
folder: TargetedTelemetry
name: Targeted telemetry
version: 1.0.0
targets:
  - scheduled-conference
YAML);
        Storage::disk('plugins')->put('TargetedTelemetry/index.php', <<<'PHP'
<?php

return new class extends \App\Classes\Plugin {};
PHP);
        $setting = new PluginSetting([
            'conference_id' => $conference->getKey(),
            'scheduled_conference_id' => 0,
            'plugin' => 'TargetedTelemetry',
            'key' => 'enabled',
            'value' => '1',
            'type' => 'boolean',
        ]);
        $setting->timestamps = false;
        $setting->save();

        app()->setCurrentConferenceId($conference->getKey());
        app()->setCurrentScheduledConferenceId($firstSchedule->getKey());
        $payload = app(TelemetrySnapshotBuilder::class)->build();

        $targetedPlugin = collect($payload['feature_adoption']['enabled_plugins'])
            ->firstWhere('name', 'TargetedTelemetry');
        $this->assertSame(1, $targetedPlugin['scheduled_conferences']);
    }

    public function test_registration_adoption_uses_the_scheduled_conference_default(): void
    {
        $conference = Conference::query()->create([
            'name' => 'Default Registration Conference',
            'path' => 'default-registration',
        ]);
        $this->scheduledConference($conference, 'schedule-with-defaults', []);

        $payload = app(TelemetrySnapshotBuilder::class)->build();

        $this->assertSame(1, $payload['feature_adoption']['scheduled_conferences_using_registration']);
    }

    public function test_version_check_is_pure_and_does_not_send_registry_or_telemetry_fields(): void
    {
        Cache::forget('get_latest_version');
        Http::fake([
            '*checkversion' => Http::response(['tag' => '1.5.0'], 200),
            '*' => Http::response([], 200),
        ]);

        CheckLatestVersion::run();

        Http::get('https://unrelated.example.com/health');

        Http::assertSent(function (HttpRequest $request): bool {
            return str_ends_with($request->url(), '/api/checkversion')
                && $request->data() === []
                && ! str_contains($request->url(), 'unique_id')
                && ! str_contains($request->url(), 'meta')
                && $request->hasHeader('Leconfe-Telemetry-Client');
        });

        Http::assertSent(function (HttpRequest $request): bool {
            return str_contains($request->url(), 'unrelated.example.com')
                && ! $request->hasHeader('Leconfe-Telemetry-Client');
        });
    }

    public function test_installation_notice_contains_opt_out_and_non_content_data_boundary(): void
    {
        Config::set('app.installed', false);

        Livewire::test(\App\Frontend\Website\Pages\Installation::class)
            ->assertSet('form.telemetry_enabled', true)
            ->assertSee('Installation and usage telemetry')
            ->assertSee('Opting out stops future sends but does not delete historical data.')
            ->assertSee('Last send status/time: not sent yet.')
            ->assertSee('submission content/titles');
    }

    public function test_upgrade_notice_is_shown_once_and_settings_surface_reflects_the_toggle(): void
    {
        Setting::set(TelemetrySettings::UPGRADE_NOTICE_PENDING, true);
        Setting::set(TelemetrySettings::UPGRADE_NOTICE_SHOWN, false);
        $admin = User::query()->create([
            'given_name' => 'Admin',
            'family_name' => 'Example',
            'email' => 'upgrade-admin@example.com',
            'password' => Hash::make('password'),
        ]);
        $this->actingAs($admin);

        Livewire::test(\App\Frontend\Website\Pages\InstallationSuccessful::class)
            ->assertSee('What changed in this upgrade')
            ->assertSee('Opting out stops future sends but does not delete historical data.');

        $this->assertFalse((bool) Setting::get(TelemetrySettings::UPGRADE_NOTICE_PENDING));
        $this->assertTrue((bool) Setting::get(TelemetrySettings::UPGRADE_NOTICE_SHOWN));

        Livewire::test(\App\Frontend\Website\Pages\InstallationSuccessful::class)
            ->assertDontSee('What changed in this upgrade');

        Setting::set(TelemetrySettings::ENABLED, false);
        Livewire::test(\App\Panel\Administration\Livewire\TelemetrySetting::class)
            ->assertSet('formData.telemetry_enabled', false)
            ->assertSee('Last send');
    }

    public function test_pending_upgrade_notice_is_shown_once_in_admin_telemetry_settings(): void
    {
        Setting::set(TelemetrySettings::UPGRADE_NOTICE_PENDING, true);
        Setting::set(TelemetrySettings::UPGRADE_NOTICE_SHOWN, false);
        $admin = User::query()->create([
            'given_name' => 'CLI',
            'family_name' => 'Upgrade',
            'email' => 'cli-upgrade@example.com',
            'password' => Hash::make('password'),
        ]);
        $this->actingAs($admin);

        Livewire::test(\App\Panel\Administration\Livewire\TelemetrySetting::class)
            ->assertSee('What changed in this upgrade');
        $this->assertTrue(app(TelemetrySettings::class)->upgradeNoticeShown());

        Livewire::test(\App\Panel\Administration\Livewire\TelemetrySetting::class)
            ->assertDontSee('What changed in this upgrade');
    }

    private function scheduledConference(Conference $conference, string $path, array $meta): ScheduledConference
    {
        $scheduledConference = ScheduledConference::query()->create([
            'conference_id' => $conference->getKey(),
            'title' => $path,
            'path' => $path,
            'is_published' => true,
        ]);
        $scheduledConference->setManyMeta($meta);

        return $scheduledConference;
    }
}
