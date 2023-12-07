<?php

namespace App\Managers;

use Exception;
use ZipArchive;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Filesystem\Filesystem;
use App\Models\Plugin as ModelsPlugin;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use App\Classes\Plugin as ClassesPlugin;

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

    protected function getPluginFullPath($pluginPath): string
    {
        return base_path($pluginPath);
    }

    protected function extractPlugin(string $filePath, string $to): bool
    {
        try {
            $zip = new ZipArchive();
        } catch (\Throwable $th) {
            throw new Exception("PHP zip extension is not installed");
        }


        if (pathinfo($filePath)['extension'] != 'zip') {
            File::delete($filePath);
            throw new Exception("Plugin extension must be .zip");
        }


        if ($zip->open($filePath) !== true) {
            File::delete($filePath);
            throw new Exception("Cannot open the zip, please check the zip file");
        }

        try {

            $extracted = $zip->extractTo(storage_path($to));

            $zip->close();


            File::delete($filePath);

            return $extracted;
        } catch (\Throwable $th) {
            File::delete($filePath);
            throw new Exception("Cannot extract the plugin, please check the zip file");
        }
    }


    public function pluginInstall(string $file): void
    {

        $storagePlugin = 'app' . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR;

        $filePath = storage_path($storagePlugin . $file);

        $temp = $storagePlugin . '.temp';

        $this->extractPlugin($filePath, $temp);

        $plugin = scandir(storage_path($temp));

        $pluginPath = 'plugins' . DIRECTORY_SEPARATOR . $plugin[2];

        $filesystem = new Filesystem();

        $filesystem->moveDirectory(storage_path($temp . DIRECTORY_SEPARATOR . $plugin[2]), $this->getPluginFullPath($pluginPath), true);

        $currentPlugin = $this->readPlugin($pluginPath);

        $pluginInfo = $currentPlugin->aboutPlugin;


        ModelsPlugin::updateOrCreate(
            [
                'name' => $pluginInfo['plugin_name'],
                'author' => $pluginInfo['author'],
            ],
            [
                'description' => $pluginInfo['description'],
                'version' => $pluginInfo['version'],
                'path' => $pluginPath,
                'is_active' => false,
            ]
        );
        $currentPlugin->onInstall();
    }

    protected function readPlugin(string $pluginPath)
    {
        $pluginFullPath = $this->getPluginFullPath($pluginPath);

        $pluginIndex = base_path($pluginPath . DIRECTORY_SEPARATOR . 'index.php');

        if (!file_exists($pluginIndex)) {
            $this->handleErrorAndCleanup($pluginFullPath, "index.php is not found in {$pluginPath}.");
        }

        $currentPlugin = require $pluginIndex;

        if (!$currentPlugin instanceof ClassesPlugin) {
            $this->handleErrorAndCleanup($pluginFullPath, "index.php in {$pluginPath} must return an instance of App\\Classes\\Plugin");
        }

        $validValues = ['plugin_name', 'author', 'description', 'version', 'is_active'];

        try {
            $about = File::json(base_path($pluginPath . DIRECTORY_SEPARATOR . 'about.json'));

            foreach ($validValues as $validValue) {
                if (!array_key_exists($validValue, $about)) {
                    $this->handleErrorAndCleanup($pluginFullPath, "about.json in {$pluginPath} is not valid, key \"{$validValue}\" is not found.");
                }
            }
        } catch (\Throwable $th) {
            $this->handleErrorAndCleanup($pluginFullPath, "about.json is not found in {$pluginPath}.");
        }

        $currentPlugin->aboutPlugin = $about;

        return $currentPlugin;
    }

    public function pluginUninstall(ModelsPlugin $record): void
    {
        $plugin = $this->readPlugin($record->path);

        $plugin->onUninstall();

        $filesystem = new Filesystem();

        $filesystem->deleteDirectory($this->getPluginFullPath($record->path));

        $record->delete();
    }

    protected function runPlugins(): void
    {
        $this->isDatabaseConnected() && $this->pluginTableIsExist() ? $this->getPluginFromDatabase() : $this->getPluginFromDirectory();
    }


    public function scanPlugins()
    {
        $pluginsList = array_diff(scandir(base_path('plugins')), ['.', '..', '.temp']);

        $pluginDir = 'plugins' . DIRECTORY_SEPARATOR;

        foreach ($pluginsList as $plugin) {

            $pluginInstance = $this->readPlugin($pluginDir . $plugin);

            $about = $pluginInstance->aboutPlugin;

            ModelsPlugin::updateOrCreate(
                [
                    'name' => $about['plugin_name'],
                    'author' => $about['author'],
                ],
                [
                    'description' => $about['description'],
                    'version' => $about['version'],
                    'path' => $pluginDir . $plugin,
                    'is_active' => false,
                ]
            );
        }
    }


    protected function handleErrorAndCleanup($pluginFullPath, $errorMessage)
    {
        if (app()->isProduction()) {
            File::isDirectory($pluginFullPath) ? File::deleteDirectory($pluginFullPath) : File::delete($pluginFullPath);
        }
        throw new Exception($errorMessage);
    }


    protected function getPluginFromDirectory(): void
    {

        if (!File::exists(base_path('plugins'))) {
            File::makeDirectory(base_path('plugins'));
        }

        $pluginsList = array_diff(scandir(base_path('plugins')), ['.', '..', '.temp']);

        $pluginDir = 'plugins' . DIRECTORY_SEPARATOR;

        foreach ($pluginsList as $plugin) {

            $pluginInstance = $this->readPlugin($pluginDir . $plugin);

            $pluginInfo = $pluginInstance->aboutPlugin;

            $this->plugins[$pluginInfo['plugin_name']] = $pluginInstance;

            $this->plugins[$pluginInfo['plugin_name']]->boot();
        }
    }

    protected function getPluginFromDatabase(): void
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

    protected function isDatabaseConnected(): bool
    {
        try {
            DB::connection()->getPdo();
        } catch (\Throwable $th) {
            return false;
        }

        return true;
    }

    protected function pluginTableIsExist(): bool
    {
        return Schema::hasTable('plugins');
    }
}
