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
            // C:\Users\J\Documents\leconfe\storage\app\plugins\.temp

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
        $this->createPluginDirectory();


        $storagePlugin = 'app' . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR;
        // app\plugins\

        $filePath = storage_path($storagePlugin . $file);
        // "C:\Users\J\Documents\leconfe\storage\app\plugins\Order.zip"


        $temp = $storagePlugin . '.temp';
        // "app\plugins\.temp"

        $this->extractPlugin($filePath, $temp);
        // extract plugin goes here if true -> storage\app\plugins\.temp


        $plugin = scandir(storage_path($temp));
        // "C:\Users\J\Documents\leconfe\storage\app\plugins\.temp"


        $pluginPath = 'plugins' . DIRECTORY_SEPARATOR . $plugin[2];
        // "plugins\Order"


        $filesystem = new Filesystem();


        $filesystem->moveDirectory(storage_path($temp . DIRECTORY_SEPARATOR . $plugin[2]), $this->getPluginFullPath($pluginPath), true);
        // from C:\Users\J\Documents\leconfe\storage\app\plugins\.temp\Order
        // to C:\Users\J\Documents\leconfe\plugins\Order



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
        $plugins = DB::table('plugins')->where('is_active', true)->get(); // connection() is yet running on register(), cannot querying using Model::class

        foreach ($plugins as $plugin) {
            if ($pluginInstance = $this->readPlugin($plugin->path)) {
                $pluginInfo = $pluginInstance->aboutPlugin;

                $this->plugins[$pluginInfo['plugin_name']] = $pluginInstance;
                $this->plugins[$pluginInfo['plugin_name']]->boot();
            }
        }
    }

    protected function readPlugin(string $pluginPath)
    {
        // ->pluginPath "plugins\Order"
        $pluginFullPath = $this->getPluginFullPath($pluginPath);
        // "C:\Users\J\Documents\leconfe\plugins\Order"


        $pluginIndex = base_path($pluginPath . DIRECTORY_SEPARATOR . 'index.php');
        // "C:\Users\J\Documents\leconfe\plugins\Order\index.php"



        // Check if index.php exists in plugin directory
        if (!file_exists($pluginIndex)) {
            $this->handleErrorAndCleanup($pluginFullPath, "index.php is not found in {$pluginPath}.");
        }

        //TODO : make sure to run composer dump-autoload before execute this line, it will throw error classes not found
        $currentPlugin = require $pluginIndex;



        if (!$currentPlugin instanceof ClassesPlugin) {
            $this->handleErrorAndCleanup($pluginFullPath, "index.php in {$pluginPath} must return an instance of App\\Classes\\Plugin");
        }

        // prepare valid about.json
        $validValues = ['plugin_name', 'author', 'description', 'version'];

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

    public function createPluginDirectory(): void
    {
        if (!File::exists(base_path('plugins'))) {
            File::makeDirectory(base_path('plugins'));
        }
    }

    protected function handleErrorAndCleanup($pluginFullPath, $errorMessage)
    {
        if (app()->isProduction()) {
            File::deleteDirectory($pluginFullPath);
        }
        throw new Exception($errorMessage);
    }
}
