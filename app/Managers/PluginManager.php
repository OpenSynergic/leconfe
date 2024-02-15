<?php

namespace App\Managers;

use App\Application;
use App\Classes\Plugin as ClassesPlugin;
use App\Events\PluginInstalled;
use App\Models\Conference;
use Exception;
use Illuminate\Contracts\Filesystem\Filesystem as FilesystemContract;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Symfony\Component\Yaml\Yaml;
use ZipArchive;

class PluginManager
{
    protected Collection $registeredPlugins;

    protected Collection $bootedPlugins;

    protected bool $isBooted = false;

    public function boot()
    {
        


        $this->bootPlugins();
    }

    public function getDisk(): FilesystemContract
    {
        return Storage::disk('plugins');
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
            ->mapWithKeys(fn ($pluginDir) => [$pluginDir => Yaml::parseFile($pluginsDisk->path($pluginDir . DIRECTORY_SEPARATOR . 'index.yaml'))]);
    }

    public function getPluginInfo($path)
    {
        return $this->getRegisteredPlugins()->get($path);
    }

    protected function bootPlugins($includeDisabled = false, $refresh = false): void
    {
        if ($this->isBooted && !$refresh) {
            return;
        }

        $this->bootedPlugins = $this->getRegisteredPlugins()
            ->when(
                !$includeDisabled,
                fn ($plugins) => $plugins
                    ->filter(fn ($pluginInfo, $pluginPath) => $this->getSetting($pluginPath, 'enabled') && $this->loadPlugin($this->getDisk()->path($pluginPath), false))
            )
            ->mapWithKeys(fn ($pluginInfo, $pluginPath) => [$pluginPath => $this->bootPlugin($this->getDisk()->path($pluginPath))]);

        $this->isBooted = true;
    }

    public function bootPlugin($pluginPath): ?ClassesPlugin
    {
        $plugin = require $pluginPath . DIRECTORY_SEPARATOR . 'index.php';
        $plugin->setPluginPath($pluginPath);
        $plugin->load();
        $plugin->boot();

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

        return $plugin;
    }

    public function getRegisteredPlugins(): Collection
    {
        if (empty($this->registeredPlugins)) {
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

    public function getPlugin($path, $onlyEnabled = true) : ?ClassesPlugin
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

    public function getSetting(string $plugin, $key): mixed
    {
        return once(fn () => DB::table('plugin_settings')
            ->where('conference_id', App::getCurrentConferenceId())
            ->where('plugin', $plugin)
            ->where('key', $key)
            ->value('value'));
    }

    public function updateSetting(string $plugin, $key, $value): mixed
    {
        // Flush cache
        \Spatie\Once\Cache::getInstance()->flush();

        return DB::table('plugin_settings')
            ->updateOrInsert(
                [
                    'plugin' => $plugin,
                    'key' => $key,
                    'conference_id' => app()->getCurrentConference()?->getKey() ?? Application::CONTEXT_WEBSITE,
                ],
                ['value' => $value],
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

            if(!isset($pluginInfo['name'])) {
                throw new Exception('Plugin must contain a name');
            }

            if(!isset($pluginInfo['folder'])) {
                throw new Exception('Plugin must contain a folder with the same name as the plugin folder name');
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

    public function installDefaultPlugins(Conference $conference)
    {
        $defaultPluginsPath = base_path('stubs' . DIRECTORY_SEPARATOR . 'plugins');

        $pluginsDisk = $this->getDisk($conference);

        foreach (File::directories($defaultPluginsPath) as $pluginPath) {
            $pluginName = basename($pluginPath);

            if ($pluginsDisk->exists($pluginName)) {
                continue;
            }

            $this->validatePlugin($pluginPath);

            $plugin = $this->loadPlugin($pluginPath);
            $plugin->boot();

            $fileSystem = new Filesystem();
            $fileSystem->copyDirectory($pluginPath, $pluginsDisk->path($pluginName));
        }
    }
}
