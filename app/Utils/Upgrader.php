<?php

namespace App\Utils;

use App\Services\Telemetry\TelemetrySettings;
use App\Utils\Enums\UpgradeActionPriority;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class Upgrader extends Installer
{
    public array $actions = [];

    public string $installedVersion;

    public string $codeVersion;

    public function __construct(
        public array $params = [],
        public ?Command $command = null,
    ) {
        $this->installedVersion = App::getInstalledVersion();
        $this->codeVersion = App::getCodeVersion();

        if (version_compare($this->installedVersion, $this->codeVersion, '>=')) {
            throw new \Exception('Your application is already up to date!');
        }
    }

    public function run()
    {
        try {
            // count time to upgrade
            $start = microtime(true);

            $this->prepareActions();

            $this->startActions();

            $this->addNewApplicationVersion();

            $this->configureOptimization();
            $telemetrySettings = app(TelemetrySettings::class);
            if (array_key_exists('telemetry_enabled', $this->params)) {
                $telemetrySettings->setEnabled((bool) $this->params['telemetry_enabled']);
            } elseif (config('app.beacon', true) === false) {
                $telemetrySettings->setEnabled(false);
            }
            // The upgrade notice is informational; telemetry stays default-on
            // unless the legacy beacon or persisted setting opted out.
            if (! $telemetrySettings->upgradeNoticeShown()) {
                $telemetrySettings->queueUpgradeNotice();
            }
        } catch (\Throwable $th) {
            throw $th;

            return;
        }

        $this->log('Upgrade success', [
            'duration' => round(microtime(true) - $start, 2),
            'status' => 'success',
        ]);
    }

    public function log($message, $properties = [])
    {
        activity('leconfe')
            ->causedByAnonymous()
            ->event('upgrade')
            ->withProperties([
                'from' => $this->installedVersion,
                'to' => $this->codeVersion,
                ...$properties,
            ])
            ->log($message);
    }

    public function addNewApplicationVersion()
    {
        $version = app()->getVersion();
        $version->save();

        return $version;
    }

    public function startActions()
    {
        if (empty($this->actions)) {
            return;
        }

        // Sort by priority
        ksort($this->actions);

        foreach ($this->actions as $actions) {
            foreach ($actions as $key => $action) {
                $this->command?->info("Running upgrade actions : $key");
                $action($this->command);
            }
        }
    }

    public function prepareActions()
    {
        $installedVersion = App::getInstalledVersion();
        $applicationVersion = App::getCodeVersion();
        $schemas = UpgradeSchema::getSchemasByVersion($installedVersion, $applicationVersion);
        foreach ($schemas as $key => $value) {
            $this->addAction($key, $value);
        }
    }

    public function addAction(string $name, callable $action, UpgradeActionPriority $priority = UpgradeActionPriority::MEDIUM)
    {
        $this->actions[$priority->value][$name] = $action;
    }
}
