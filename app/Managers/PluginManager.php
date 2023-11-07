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
        return $this->plugins[$pluginName] ?? false;
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

    public function extractPlugin(string $filePath, string $to): bool
    {
        try {
            $zip = new ZipArchive();
        } catch (\Throwable $th) {
            throw new Exception("PHP zip extension is not installed");
        }
        if (pathinfo($filePath)['extension'] == 'zip') {
            if ($zip->open($filePath) == true) { // Opening other than zip file is returning integer 19 not false
                try {
                    $extracted = $zip->extractTo(storage_path($to));
                } catch (\Throwable $th) {
                    File::delete($filePath);
                    throw new Exception("Cannot extract the plugin, please check the zip file");
                }
                $zip->close();
                File::delete($filePath);
                return $extracted;
            } else {
                File::delete($filePath);
                throw new Exception("Cannot open the zip, please check the zip file");
            }
        } else {
            File::delete($filePath);
            throw new Exception("Plugin extension must be .zip");
        }
    }

    public function pluginInstall(string $file): void
    {
        $storagePlugin = 'app'. DIRECTORY_SEPARATOR .'plugins' . DIRECTORY_SEPARATOR;
        $filePath = storage_path($storagePlugin . $file);
        $temp = $storagePlugin . '.temp';

        $this->extractPlugin($filePath, $temp);
        $plugin = scandir(storage_path($temp));
        $pluginPath = 'plugins' . DIRECTORY_SEPARATOR . $plugin[2];

        $filesystem = new Filesystem();
        $filesystem->moveDirectory(storage_path($temp . DIRECTORY_SEPARATOR . $plugin[2]), base_path($pluginPath), true);

        $currentPlugin = $this->readPlugin($pluginPath);
        $pluginInfo = $currentPlugin->aboutPlugin;

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
                $pluginInfo = $pluginInstance->aboutPlugin;
                
                $this->plugins[$pluginInfo['plugin_name']] = $pluginInstance;
                $this->plugins[$pluginInfo['plugin_name']]->boot();
            }
        }
    }

    public function readPlugin(string $pluginPath)
    {
        if (!file_exists(base_path($pluginPath . DIRECTORY_SEPARATOR . 'index.php'))) {
            if (app()->isProduction()) {
                File::deleteDirectory(base_path($pluginPath));
            }
            throw new Exception("index.php is not found in {$pluginPath}.");
        }
        $currentPlugin = require base_path($pluginPath . DIRECTORY_SEPARATOR . 'index.php');
        if (!$currentPlugin instanceof ClassesPlugin) {
            if (app()->isProduction()) {
                File::deleteDirectory(base_path($pluginPath));
            }
            throw new Exception("index.php in {$pluginPath} must return an instance of App\\Classes\\Plugin");
        }

        $validValues = ['plugin_name', 'author', 'description', 'version'];

        try {
            $about = File::json(base_path($pluginPath . DIRECTORY_SEPARATOR . 'about.json'));
        } catch (\Throwable $th) {
            if (app()->isProduction()) {
                File::deleteDirectory(base_path($pluginPath));
            }
            throw new Exception("about.json is not found in {$pluginPath}.");
        }
        foreach ($validValues as $validValue) {
            if (!array_key_exists($validValue, $about)) {
                if (app()->isProduction()) {
                    File::deleteDirectory(base_path($pluginPath));
                }
                throw new Exception("about.json in {$pluginPath} is not valid, key \"{$validValue}\" is not found.");
            }
        }

        $currentPlugin->aboutPlugin = $about;

        return $currentPlugin;
    }

    public function aboutPlugin(string $jsonPath)
    {
        $validValues = ['plugin_name', 'author', 'description', 'version'];

        try {
            $about = File::json(base_path($jsonPath . DIRECTORY_SEPARATOR . 'about.json'));
        } catch (\Throwable $th) {
            if (app()->isProduction()) {
                File::deleteDirectory(base_path($jsonPath));
            }
            throw new Exception("about.json is not found in {$jsonPath}.");
        }
        foreach ($validValues as $validValue) {
            if (!array_key_exists($validValue, $about)) {
                if (app()->isProduction()) {
                    File::deleteDirectory(base_path($jsonPath));
                }
                throw new Exception("about.json in {$jsonPath} is not valid, key \"{$validValue}\" is not found.");
            }
        }

        return $about;
    }

    public function scanPlugins()
    {
        $pluginsList = array_diff(scandir(base_path('plugins')), ['.', '..', '.temp']);
        $pluginDir = 'plugins' . DIRECTORY_SEPARATOR;

        foreach ($pluginsList as $plugin) {
            $about = $plugin->aboutPlugin;
            ModelsPlugin::updateOrCreate([
                'name' => $about['plugin_name'],
                'author' => $about['author'],
            ],
            [
                'description' => $about['description'],
                'version' => $about['version'],
                'path' => $pluginDir . $plugin,
                'is_active' => false,
            ]);
        }
    }
}
