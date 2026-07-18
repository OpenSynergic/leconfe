<?php

final class PluginTestRunner
{
    public function __construct(
        private readonly string $rootPath,
        private readonly ?string $pluginsPath = null,
        private readonly ?string $composerBinary = null,
    ) {}

    public function run(): int
    {
        echo "[RUN] Application: artisan test\n";

        $exitCode = $this->runCommand([PHP_BINARY, 'artisan', 'test'], $this->rootPath);

        if ($exitCode !== 0) {
            echo "[FAIL] Application: artisan test exited with code {$exitCode}\n";

            return $exitCode;
        }

        return $this->runPluginSuites();
    }

    public function runPluginSuites(): int
    {
        $pluginsPath = $this->pluginsPath ?? $this->rootPath.'/plugins';
        $pluginPaths = glob($pluginsPath.'/*', GLOB_ONLYDIR) ?: [];
        sort($pluginPaths, SORT_NATURAL | SORT_FLAG_CASE);

        $runCount = 0;
        $skipCount = 0;

        foreach ($pluginPaths as $pluginPath) {
            $pluginName = basename($pluginPath);
            $composerPath = $pluginPath.'/composer.json';

            if (! is_file($composerPath)) {
                echo "[SKIP] {$pluginName}: composer.json not found\n";
                $skipCount++;

                continue;
            }

            try {
                $composer = json_decode(file_get_contents($composerPath), true, 512, JSON_THROW_ON_ERROR);
            } catch (JsonException) {
                echo "[FAIL] {$pluginName}: invalid composer.json\n";

                return 1;
            }

            if (! is_array($composer) || array_is_list($composer)) {
                echo "[FAIL] {$pluginName}: composer.json must contain an object\n";

                return 1;
            }

            $testScript = $composer['scripts']['test'] ?? null;

            if (
                (! is_string($testScript) && ! is_array($testScript))
                || (is_string($testScript) && trim($testScript) === '')
                || (is_array($testScript) && $testScript === [])
            ) {
                echo "[SKIP] {$pluginName}: composer test script not found\n";
                $skipCount++;

                continue;
            }

            echo "[RUN] {$pluginName}: composer test\n";
            $exitCode = $this->runCommand([$this->composerBinary ?? 'composer', 'test'], $pluginPath);

            if ($exitCode !== 0) {
                echo "[FAIL] {$pluginName}: composer test exited with code {$exitCode}\n";

                return $exitCode;
            }

            $runCount++;
        }

        echo "Plugin test summary: {$runCount} run, {$skipCount} skipped.\n";

        return 0;
    }

    private function runCommand(array $command, string $workingDirectory): int
    {
        $process = proc_open($command, [
            0 => ['pipe', 'r'],
            1 => ['file', 'php://stdout', 'a'],
            2 => ['file', 'php://stderr', 'a'],
        ], $pipes, $workingDirectory);

        if (! is_resource($process)) {
            return 1;
        }

        fclose($pipes[0]);

        return proc_close($process);
    }
}

if (PHP_SAPI === 'cli' && realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === __FILE__) {
    exit((new PluginTestRunner(dirname(__DIR__)))->run());
}
