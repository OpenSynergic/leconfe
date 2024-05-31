<?php

namespace App\Managers;

use App\Classes\Plugin as ClassesPlugin;
use App\Events\PluginInstalled;
use App\Models\Conference;
use App\Models\PluginSetting;
use Exception;
use Illuminate\Contracts\Filesystem\Filesystem as FilesystemContract;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Yaml\Yaml;
use ZipArchive;

class PluginManager
{
    protected Collection $registeredPlugins;

    protected Collection $bootedPlugins;

    protected bool $isBooted = false;

    public function __construct()
    {
        $this->bootedPlugins = collect();
        $this->registeredPlugins = collect();
    }

    public function boot()
    {
        if (app()->runningInConsole()) {
            return;
        }

        $this->bootPlugins();
    }

    public function getDisk(?Conference $conference = null): FilesystemContract
    {
        $conferenceId = $conference?->getKey() ?? app()->getCurrentConferenceId();
        $pluginsPath = config('filesystems.disks.plugins.root') . DIRECTORY_SEPARATOR . $conferenceId;
        if (!File::isDirectory($pluginsPath)) {
            File::makeDirectory($pluginsPath, 0755, true);
        }
        return Storage::build([
            'driver' => 'local',
            'root' => $pluginsPath,
            'throw' => false,
            'visibility' => 'private',
        ]);
    }

    public function getTempDisk()
    {
        return Storage::disk('plugins-tmp');
    }

    public function getPluginFullPath($path)
    {
        return $this->getDisk()->path($path);
    }

    protected function registerPlugins(): void
    {
        $pluginsDisk = $this->getDisk();
        $this->registeredPlugins = collect($pluginsDisk->directories())
            ->filter(function ($pluginDir) use ($pluginsDisk) {
                try {
                    if (Str::contains($pluginDir, ' ')) {
                        throw new Exception("Plugin folder name ({$pluginDir}) cannot contain spaces");
                    }

                    if (!$pluginsDisk->exists($pluginDir . DIRECTORY_SEPARATOR . 'index.yaml')) {
                        throw new Exception("Plugin ({$pluginDir}) is missing index.yaml file");
                    }

                    if (!$pluginsDisk->exists($pluginDir . DIRECTORY_SEPARATOR . 'index.php')) {
                        throw new Exception("Plugin ({$pluginDir}) is missing index.php file");
                    }
                } catch (\Throwable $th) {
                    if (!app()->isProduction()) {
                        throw $th;
                    }

                    return false;
                }

                return true;
            })
            ->mapWithKeys(fn ($pluginDir) => [$this->getPluginFullPath($pluginDir) => Yaml::parseFile($pluginsDisk->path($pluginDir . DIRECTORY_SEPARATOR . 'index.yaml'))]);
    }

    public function getPluginInfo($path)
    {
        return $this->getRegisteredPlugins()->get($path);
    }

    protected function bootPlugins($includeDisabled = false, $refresh = false): void
    {
        if (!app()->isInstalled()) {
            return;
        }

        if ($this->isBooted && !$refresh) {
            return;
        }
        $this->getRegisteredPlugins()
            ->when(
                !$includeDisabled,
                fn ($plugins) => $plugins
                    ->filter(fn ($pluginInfo, $pluginPath) => $this->getSetting($pluginInfo['folder'], 'enabled') && $this->loadPlugin($pluginPath, false))
            )
            ->each(fn ($pluginInfo, $pluginPath) => $this->bootPlugin($pluginPath));

        $this->isBooted = true;
    }

    public function bootPlugin($pluginPath): ?ClassesPlugin
    {
        $plugin = require $pluginPath . DIRECTORY_SEPARATOR . 'index.php';
        $plugin->setPluginPath($pluginPath);
        $plugin->load();
        $plugin->boot();

        $this->bootedPlugins->put($plugin->getInfo('folder'), $plugin);

        return $plugin;
    }

    protected function loadPlugin(string $pluginPath, $throwError = true): mixed
    {
        try {
            $plugin = require $pluginPath . DIRECTORY_SEPARATOR . 'index.php';

            if (!$plugin instanceof ClassesPlugin) {
                throw new Exception('Plugin must return an instance of ' . ClassesPlugin::class);
            }
        } catch (\Throwable $th) {
            if ($throwError) {
                throw $th;
            }

            return false;
        }

        $plugin->setPluginPath($pluginPath);

        return $plugin;
    }

    public function getRegisteredPlugins(): Collection
    {
        if ($this->registeredPlugins->isEmpty()) {
            $this->registerPlugins();
        }

        return $this->registeredPlugins;
    }

    public function getPlugins()
    {
        if (empty($this->bootedPlugins)) {
            $this->boot();
        }

        return $this->bootedPlugins;
    }

    public function getPlugin($path, $onlyEnabled = true): ?ClassesPlugin
    {
        $plugin = $this->getPlugins()->get($path);

        if ($plugin || $onlyEnabled) {
            return $plugin;
        }

        return $this->bootPlugin($path);
    }

    public function enable($pluginPath, $enable = true)
    {
        $this->updateSetting($pluginPath, 'enabled', $enable);
    }

    public function disable($pluginPath)
    {
        $this->enable($pluginPath, false);
    }

    public function getSetting(string $plugin, mixed $key, $default = null): mixed
    {
        $conferenceId = App::getCurrentConferenceId();

        return Cache::rememberForever("plugin_setting_{$conferenceId}_{$plugin}_{$key}", function () use ($plugin, $key, $default, $conferenceId) {
            $setting = PluginSetting::query()
                ->where('conference_id', $conferenceId)
                ->where('plugin', $plugin)
                ->where('key', $key)
                ->first();

            return $setting ? $this->convertFromDB($setting->value, $setting->type) : $default;
        });
    }

    public function updateSetting(string $plugin, $key, $value): mixed
    {
        $conferenceId = App::getCurrentConferenceId();

        Cache::forget("plugin_setting_{$conferenceId}_{$plugin}_{$key}");

        $type = $this->getType($value);

        return PluginSetting::query()
            ->updateOrInsert(
                [
                    'plugin' => $plugin,
                    'conference_id' => $conferenceId,
                    'key' => $key,
                ],
                [
                    'value' => $this->convertToDB($value, $type, true),
                    'type' => $type,
                ],
            );
    }

    public function cleanTempPlugins()
    {
        $this->getTempDisk()->deleteDirectory('');
    }

    public function install(string $file)
    {
        $pluginTempDisk = $this->getTempDisk();

        if (!$folderName = $this->extractToTempPlugin($file)) {
            throw new Exception('Cannot extract the plugin, please check the zip file');
        }

        // if ($this->getDisk()->exists($folderName)) {
        //     throw new Exception("Plugin already installed");
        // }

        $this->validatePlugin($pluginTempDisk->path($folderName));

        try {
            $plugin = $this->loadPlugin($pluginTempDisk->path($folderName));
            $plugin->boot();
        } catch (\Throwable $th) {
            $pluginTempDisk->deleteDirectory($folderName);

            throw $th;
        }

        $fileSystem = new Filesystem();
        $fileSystem->moveDirectory($pluginTempDisk->path($folderName), $this->getDisk()->path($folderName), true);

        PluginInstalled::dispatch($plugin);

        return true;
    }

    public function validatePlugin(string $pluginPath)
    {
        if (!file_exists($pluginPath)) {
            throw new Exception("Plugin {$pluginPath} not found");
        }

        $pluginName = basename($pluginPath);

        if (Str::contains($pluginPath, ' ')) {
            throw new Exception("Plugin folder name ({$pluginName}) cannot contain spaces");
        }

        if (!file_exists($pluginPath . DIRECTORY_SEPARATOR . 'index.yaml')) {
            throw new Exception("Plugin ({$pluginName}) is missing index.yaml file");
        }

        if (!file_exists($pluginPath . DIRECTORY_SEPARATOR . 'index.php')) {
            throw new Exception("Plugin ({$pluginName}) is missing index.php file");
        }
    }

    protected function extractToTempPlugin(string $filePath): string
    {
        try {
            if (!class_exists('ZipArchive')) {
                throw new Exception('Please Install PHP Zip Extension');
            }

            if (!file_exists($filePath)) {
                throw new Exception("File {$filePath} not found");
            }

            if (pathinfo($filePath)['extension'] != 'zip') {
                throw new Exception('Plugin extension must be .zip');
            }

            $zip = new ZipArchive();
            if ($zip->open($filePath) !== true) {
                throw new Exception('Cannot open the zip, please check the zip file');
            }

            $pluginInfo = null;

            for ($i = 0; $i < $zip->numFiles; $i++) {
                $filename = $zip->getNameIndex($i);
                if (!Str::contains($filename, 'index.yaml')) {
                    continue;
                }

                $pluginInfo = Yaml::parse($zip->getFromIndex($i));
            }

            if (!$pluginInfo) {
                throw new Exception('Plugin does not contain index.yaml file');
            }

            if (!isset($pluginInfo['name'])) {
                throw new Exception('Plugin must contain a name information in the index.yaml file');
            }

            if (!isset($pluginInfo['folder'])) {
                throw new Exception('Plugin must contain a `folder` information with the same name as the plugin folder name');
            }

            if (!$zip->extractTo($this->getTempDisk()->path(''))) {
                throw new Exception('Cannot extract the zip, please check the zip file');
            }

            $zip->close();

            if (!file_exists($this->getTempDisk()->path($pluginInfo['folder']))) {
                throw new Exception('Plugin must contain a folder with the same name as the plugin folder name');
            }
        } catch (\Throwable $th) {
            throw $th;
        }

        return $pluginInfo['folder'];
    }

    public function uninstall(string $pluginPath): void
    {
        $pluginsDisk = $this->getDisk();

        $pluginsDisk->deleteDirectory($pluginPath);
    }

    /**
     * Convert a PHP variable into a string to be stored in the DB
     *
     * @param  string  $type
     * @param  bool  $nullable  True iff the value is allowed to be null.
     * @return string
     */
    public function convertToDB($value, &$type, $nullable = false)
    {
        if ($nullable && $value === null) {
            return null;
        }

        if ($type == null) {
            $type = $this->getType($value);
        }

        switch ($type) {
            case 'object':
            case 'array':
                $value = json_encode($value, JSON_UNESCAPED_UNICODE);
                break;
            case 'bool':
            case 'boolean':
                // Cast to boolean, ensuring that string
                // "false" evaluates to boolean false
                $value = ($value && $value !== 'false') ? 1 : 0;
                break;
            case 'int':
            case 'integer':
                $value = (int) $value;
                break;
            case 'float':
            case 'number':
                $value = (float) $value;
                break;
            case 'date':
                if ($value !== null) {
                    if (!is_numeric($value)) {
                        $value = strtotime($value);
                    }
                    $value = date('Y-m-d H:i:s', $value);
                }
                break;
            case 'string':
            default:
                // do nothing.
        }

        return $value;
    }

    /**
     * Convert a value from the database to a specific type
     *
     * @param  mixed  $value  Value from the database
     * @param  string  $type  Type from the database, eg `string`
     * @param  bool  $nullable  True iff the value is allowed to be null
     */
    public function convertFromDB($value, $type, $nullable = false)
    {
        if ($nullable && $value === null) {
            return null;
        }
        switch ($type) {
            case 'bool':
            case 'boolean':
                return (bool) $value;
            case 'int':
            case 'integer':
                return (int) $value;
            case 'float':
            case 'number':
                return (float) $value;
            case 'object':
            case 'array':
                $decodedValue = json_decode($value, true);

                return !is_null($decodedValue) ? $decodedValue : [];
            case 'date':
                return strtotime($value);
            case 'string':
            default:
                // Nothing required.
                break;
        }

        return $value;
    }

    public function getType($value)
    {
        switch (gettype($value)) {
            case 'boolean':
            case 'bool':
                return 'bool';
            case 'integer':
            case 'int':
                return 'int';
            case 'double':
            case 'float':
                return 'float';
            case 'array':
            case 'object':
                return 'object';
            case 'string':
            default:
                return 'string';
        }
    }

    public function installDefaultPlugins(Conference $conference)
    {
        $defaultPluginsPath = base_path('stubs' . DIRECTORY_SEPARATOR . 'plugins');
        $pluginsDisk = $this->getDisk($conference);
        foreach (File::directories($defaultPluginsPath) as $pluginPath) {
            $pluginName = basename($pluginPath);
            if ($pluginsDisk->exists($pluginName)) {
                continue;
            }
            $fileSystem = new Filesystem();
            $fileSystem->copyDirectory($pluginPath, $pluginsDisk->path($pluginName));
        }
    }

}
