<?php

namespace App\Managers;

use App\Classes\Plugin as ClassesPlugin;
use App\Models\Plugin as ModelsPlugin;
use Exception;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class PluginManager
{
    protected array $plugins = [];

    public function boot()
    {
        $this->runPlugins();
    }

    public function getPlugins(): array
    {
        return $this->plugins;
    }

    public function getPlugin(string $pluginName)
    {
        if ($plugin = $this->plugins[$pluginName] ?? false) {
            return $plugin;
        } else {
            return false; // false means not running
        }
    }

    public function pluginActivation(string $pluginPath): void
    {
        $plugin = $this->readPlugin($pluginPath);
        $plugin->onActivation();
    }

    public function pluginDeactivation(string $pluginPath): void
    {
        $plugin = $this->readPlugin($pluginPath);
        $plugin->onDeactivation();
    }

    public function extractPlugin(string $filePath, string $to)
    {
        $zip = new ZipArchive();
        $zip->open($filePath);
        $zip->extractTo(storage_path($to));
        $zip->close();

        File::delete($filePath);
    }

    public function pluginInstall(string $file): void
    {
        $filePath = storage_path("app/plugins/{$file}");
        $temp = 'app/plugins/.temp';

        $this->extractPlugin($filePath, $temp);
        $plugin = scandir(storage_path($temp));
        $pluginPath = "plugins/{$plugin[2]}";

        $filesystem = new Filesystem();
        $filesystem->moveDirectory(storage_path("{$temp}/{$plugin[2]}"), base_path($pluginPath), true);

        $currentPlugin = $this->readPlugin($pluginPath);
        $pluginInfo = $this->aboutPlugin($pluginPath);

        ModelsPlugin::updateOrCreate([
            'name' => $pluginInfo['plugin_name'],
            'author' => $pluginInfo['author'],
        ],
        [
            'description' => $pluginInfo['description'],
            'version' => $pluginInfo['version'],
            'path' => $pluginPath,
            'is_active' => false,
        ]);

        $currentPlugin->onInstall();
    }

    public function pluginUninstall(ModelsPlugin $record): void
    {
        $plugin = $this->readPlugin($record->path);
        $plugin->onUninstall();

        Storage::disk('plugin-directory')->deleteDirectory($record->path);
        $record->delete();
    }

    public function runPlugins(): void
    {
        $plugins = DB::table('plugins')->where('is_active', true)->get(); // connection() is yet running on register(), cannot querying using Model::class

        foreach ($plugins as $plugin) {
            if ($pluginInstance = $this->readPlugin($plugin->path)) {
                $pluginInfo = $this->aboutPlugin($plugin->path);
                
                if ($pluginInstance instanceof ClassesPlugin) {
                    $this->activePlugins[$pluginInfo['plugin_name']] = $pluginInstance;
                    $this->activePlugins[$pluginInfo['plugin_name']]->boot();
                } else {
                    $this->activePlugins[$pluginInfo['plugin_name']] = false; // False means invalid
                }
            }
        }
    }

    public function readPlugin(string $pluginPath)
    {
        try {
            $currentPlugin = include base_path($pluginPath . '/index.php');
        } catch (\Throwable $th) {
            File::deleteDirectory(base_path($pluginPath));
            throw new Exception("index.php is not found in {$pluginPath}.");
        }
        if (!$currentPlugin instanceof ClassesPlugin) {
            File::deleteDirectory(base_path($pluginPath));
            throw new Exception("index.php must return an instance of App\\Classes\\Plugin");
        }

        return $currentPlugin;
    }

    public function aboutPlugin(string $jsonPath)
    {
        $validValues = ['plugin_name', 'author', 'description', 'version'];

        try {
            $about = File::json(base_path($jsonPath . '/about.json'));
        } catch (\Throwable $th) {
            File::deleteDirectory(base_path($jsonPath));
            throw new Exception("about.json is not found in {$jsonPath}.");
        }
        foreach ($validValues as $validValue) {
            if (!array_key_exists($validValue, $about)) {
                File::deleteDirectory(base_path($jsonPath));
                throw new Exception("about.json is not valid, key \"{$validValue}\" is not found.");
            }
        }

        return $about;
    }

    public function scanPlugins()
    {
        $pluginsList = array_diff(scandir(base_path('plugins')), ['.', '..', '.temp']);

        foreach ($pluginsList as $plugin) {
            $about = $this->aboutPlugin("plugins/{$plugin}");
            ModelsPlugin::updateOrCreate([
                'name' => $about['plugin_name'],
                'author' => $about['author'],
            ],
            [
                'description' => $about['description'],
                'version' => $about['version'],
                'path' => "plugins/{$plugin}",
                'is_active' => false,
            ]);
        }
    }
}
