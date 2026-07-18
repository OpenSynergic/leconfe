<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Tests\TestCase;

class PluginTestRunnerTest extends TestCase
{
    private string $pluginsPath;

    private string $composerBinary;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pluginsPath = sys_get_temp_dir().'/leconfe-plugin-tests-'.Str::uuid();
        File::makeDirectory($this->pluginsPath);

        $this->composerBinary = $this->pluginsPath.'/composer';
        file_put_contents($this->composerBinary, <<<'SH'
#!/bin/sh
case "$PWD" in
  *FailingPlugin) exit 7 ;;
esac
exit 0
SH
        );
        chmod($this->composerBinary, 0755);

        require_once base_path('scripts/test.php');
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->pluginsPath);

        parent::tearDown();
    }

    public function test_plugin_test_runner_runs_declared_tests_and_skips_the_rest(): void
    {
        $this->makePlugin('NoComposer');
        $this->makePlugin('NoTestScript', ['scripts' => ['test' => []]]);
        $this->makePlugin('PassingPlugin', ['scripts' => ['test' => 'phpunit']]);

        ob_start();
        $exitCode = $this->runner()->runPluginSuites();
        $output = ob_get_clean();

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('[SKIP] NoComposer: composer.json not found', $output);
        $this->assertStringContainsString('[SKIP] NoTestScript: composer test script not found', $output);
        $this->assertStringContainsString('[RUN] PassingPlugin: composer test', $output);
        $this->assertStringContainsString('Plugin test summary: 1 run, 2 skipped.', $output);
    }

    public function test_plugin_test_runner_stops_after_a_plugin_test_failure(): void
    {
        $this->makePlugin('FailingPlugin', ['scripts' => ['test' => 'phpunit']]);
        $this->makePlugin('LaterPlugin', ['scripts' => ['test' => 'phpunit']]);

        ob_start();
        $exitCode = $this->runner()->runPluginSuites();
        $output = ob_get_clean();

        $this->assertSame(7, $exitCode);
        $this->assertStringContainsString('[FAIL] FailingPlugin: composer test exited with code 7', $output);
        $this->assertStringNotContainsString('[RUN] LaterPlugin: composer test', $output);
    }

    public function test_plugin_test_runner_fails_when_composer_manifest_is_not_an_object(): void
    {
        $path = $this->pluginsPath.'/InvalidPlugin';
        File::makeDirectory($path);
        file_put_contents($path.'/composer.json', '[]');

        ob_start();
        $exitCode = $this->runner()->runPluginSuites();
        $output = ob_get_clean();

        $this->assertSame(1, $exitCode);
        $this->assertStringContainsString('[FAIL] InvalidPlugin: composer.json must contain an object', $output);
    }

    private function runner(): \PluginTestRunner
    {
        return new \PluginTestRunner(base_path(), $this->pluginsPath, $this->composerBinary);
    }

    private function makePlugin(string $name, ?array $composer = null): void
    {
        $path = $this->pluginsPath.'/'.$name;
        File::makeDirectory($path);

        if ($composer !== null) {
            file_put_contents($path.'/composer.json', json_encode($composer, JSON_THROW_ON_ERROR));
        }
    }
}
