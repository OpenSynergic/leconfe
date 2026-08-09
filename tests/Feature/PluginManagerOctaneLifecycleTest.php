<?php

namespace Tests\Feature;

use App\Classes\DefaultTheme;
use App\Classes\ManualPaymentPlugin;
use App\Classes\Plugin as PluginObject;
use App\Facades\Plugin;
use App\Http\Middleware\DetectConferenceContext;
use App\Managers\PluginManager;
use App\Models\Conference;
use App\Providers\PluginServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\File;
use Laravel\Octane\FrankenPhp\ServerProcessInspector;
use Mockery;
use ReflectionMethod;
use Tests\TestCase;
use Throwable;
use ZipArchive;

class PluginManagerOctaneLifecycleTest extends TestCase
{
    use RefreshDatabase;

    private string $testDirectory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->testDirectory = storage_path('framework/testing/plugin-manager-'.uniqid());
        File::makeDirectory($this->testDirectory, 0755, true);

        config()->set('filesystems.disks.plugins.root', $this->testDirectory.'/plugins');
        config()->set('filesystems.disks.plugins-tmp.root', $this->testDirectory.'/plugins-tmp');
        File::makeDirectory($this->testDirectory.'/plugins', 0755, true);
        File::makeDirectory($this->testDirectory.'/plugins-tmp', 0755, true);
        Event::fake();
    }

    protected function tearDown(): void
    {
        unset($_SERVER['LARAVEL_OCTANE']);
        File::deleteDirectory($this->testDirectory);

        parent::tearDown();
    }

    public function test_install_and_update_schedule_exactly_one_reload_after_termination(): void
    {
        $_SERVER['LARAVEL_OCTANE'] = '1';
        $inspector = Mockery::mock(ServerProcessInspector::class);
        $inspector->shouldReceive('reloadServer')->once();
        $this->app->instance(ServerProcessInspector::class, $inspector);
        $manager = new PluginManager;
        $zip = $this->makePluginZip('LifecyclePlugin');

        $manager->install($zip);
        $manager->install($zip);

        $inspector->shouldNotHaveReceived('reloadServer');

        $this->app->terminate();
    }

    public function test_uninstall_deletes_plugin_before_reloading_workers(): void
    {
        $_SERVER['LARAVEL_OCTANE'] = '1';
        File::makeDirectory($pluginDirectory = $this->testDirectory.'/plugins/RemovedPlugin');
        $inspector = Mockery::mock(ServerProcessInspector::class);
        $inspector->shouldReceive('reloadServer')
            ->once()
            ->andReturnUsing(fn () => $this->assertDirectoryDoesNotExist($pluginDirectory));
        $this->app->instance(ServerProcessInspector::class, $inspector);

        (new PluginManager)->uninstall('RemovedPlugin');

        $this->assertDirectoryExists($pluginDirectory);
        $inspector->shouldNotHaveReceived('reloadServer');

        $this->app->terminate();
    }

    public function test_classic_php_install_does_not_reload_workers(): void
    {
        $inspector = Mockery::mock(ServerProcessInspector::class);
        $inspector->shouldNotReceive('reloadServer');
        $this->app->instance(ServerProcessInspector::class, $inspector);

        (new PluginManager)->install($this->makePluginZip('ClassicPlugin'));

        $this->app->terminate();
    }

    public function test_failed_install_does_not_schedule_a_reload(): void
    {
        $_SERVER['LARAVEL_OCTANE'] = '1';
        $inspector = Mockery::mock(ServerProcessInspector::class);
        $inspector->shouldNotReceive('reloadServer');
        $this->app->instance(ServerProcessInspector::class, $inspector);

        $failed = false;

        try {
            (new PluginManager)->install($this->testDirectory.'/missing.zip');
        } catch (Throwable) {
            $failed = true;
        }

        $this->assertTrue($failed);
        $this->app->terminate();
    }

    public function test_new_managers_create_fresh_plugin_objects(): void
    {
        $pluginDirectory = $this->makePluginDirectory('FreshPlugin');
        $method = new ReflectionMethod(PluginManager::class, 'initiatePlugin');

        $first = $method->invoke(new PluginManager, $pluginDirectory);
        $second = $method->invoke(new PluginManager, $pluginDirectory);

        $this->assertInstanceOf(PluginObject::class, $first);
        $this->assertNotSame($first, $second);
    }

    public function test_each_new_scoped_manager_contains_core_plugins(): void
    {
        Plugin::clearResolvedInstance('plugin');
        $this->app->forgetInstance('plugin');
        $first = app('plugin');

        Plugin::clearResolvedInstance('plugin');
        $this->app->forgetInstance('plugin');
        $second = app('plugin');

        $this->assertNotSame($first, $second);
        $this->assertInstanceOf(DefaultTheme::class, $second->getPlugin('DefaultTheme'));
        $this->assertInstanceOf(ManualPaymentPlugin::class, $second->getPlugin('ManualPayment'));

        $second->reinitialize();

        $this->assertInstanceOf(DefaultTheme::class, $second->getPlugin('DefaultTheme'));
        $this->assertInstanceOf(ManualPaymentPlugin::class, $second->getPlugin('ManualPayment'));
    }

    public function test_default_plugin_collection_keeps_disabled_plugins_out_of_ui_registration(): void
    {
        $manager = new PluginManager;
        $plugin = new class extends PluginObject
        {
            public function load(): static
            {
                return $this;
            }

            public function isEnabled(): bool
            {
                return false;
            }
        };
        $manager->register('DisabledPlugin', $plugin);

        $this->assertFalse($manager->getPlugins()->has('DisabledPlugin'));
        $this->assertTrue($manager->getPlugins(false)->has('DisabledPlugin'));
    }

    public function test_context_middleware_initializes_plugins_after_detecting_the_request_context(): void
    {
        $conference = Conference::query()->create([
            'name' => 'Plugin Context',
            'path' => 'plugin-context',
        ]);
        $manager = Mockery::mock(PluginManager::class);
        $manager->shouldReceive('ensureCurrentContextInitialized')
            ->once()
            ->andReturnUsing(fn () => $this->assertSame($conference->getKey(), app()->getCurrentConferenceId()));
        $this->app->instance('plugin', $manager);
        Plugin::clearResolvedInstance('plugin');

        app(DetectConferenceContext::class)->handle(
            Request::create('/plugin-context'),
            fn () => response('ok'),
        );
    }

    public function test_classic_provider_detects_conference_before_plugin_initialization(): void
    {
        $conference = Conference::query()->create([
            'name' => 'Classic Context',
            'path' => 'classic-context',
        ]);
        $manager = Mockery::mock(PluginManager::class);
        $manager->shouldReceive('registerCorePlugins')->once();
        $manager->shouldReceive('initialize')
            ->once()
            ->andReturnUsing(fn () => $this->assertSame($conference->getKey(), app()->getCurrentConferenceId()));

        $this->initializePluginManager($manager, Request::create('/classic-context'), false);
    }

    public function test_octane_provider_skips_request_context_detection(): void
    {
        $_SERVER['LARAVEL_OCTANE'] = '1';
        Conference::query()->create([
            'name' => 'Octane Context',
            'path' => 'octane-context',
        ]);
        app()->resetCurrentContext();
        $manager = Mockery::mock(PluginManager::class);
        $manager->shouldReceive('registerCorePlugins')->once();
        $manager->shouldReceive('initialize')
            ->once()
            ->andReturnUsing(fn () => $this->assertSame(0, app()->getCurrentConferenceId()));

        $this->initializePluginManager($manager, Request::create('/octane-context'), false);
    }

    private function makePluginZip(string $name): string
    {
        $source = $this->makePluginDirectory($name, $this->testDirectory.'/source');
        $zipPath = $this->testDirectory.'/'.$name.'.zip';
        $zip = new ZipArchive;
        $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $zip->addFile($source.'/index.yaml', $name.'/index.yaml');
        $zip->addFile($source.'/index.php', $name.'/index.php');
        $zip->close();

        return $zipPath;
    }

    private function initializePluginManager(PluginManager $manager, Request $request, bool $runningInConsole): void
    {
        $method = new ReflectionMethod(PluginServiceProvider::class, 'initializePluginManager');
        $method->invoke(new PluginServiceProvider($this->app), $manager, $request, $runningInConsole);
    }

    private function makePluginDirectory(string $name, ?string $parent = null): string
    {
        $directory = ($parent ?? $this->testDirectory).'/'.$name;
        File::makeDirectory($directory, 0755, true);
        File::put($directory.'/index.yaml', "name: {$name}\nfolder: {$name}\nversion: 1.0.0\n");
        File::put($directory.'/index.php', '<?php return new class extends \\App\\Classes\\Plugin {};');

        return $directory;
    }
}
