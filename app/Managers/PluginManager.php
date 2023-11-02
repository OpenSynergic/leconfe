<?php

namespace App\Managers;

use App\Classes\Plugin as ClassesPlugin;
use App\Models\Plugin as ModelsPlugin;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class PluginManager
{
    protected array $activePlugins = [];

    public function boot()
    {
        $this->runPlugins();
    }

    public function getActivePlugins(): array
    {
        return $this->activePlugins;
    }

    public function getPlugin(string $pluginName): ?object
    {
        if ($plugin = $this->activePlugins[$pluginName] ?? false) {
            return $plugin;
        } else if ($plugin = DB::table('plugins')->where('name', $pluginName)->first()) {
            return $this->readPlugin($plugin->path);
        }

        return null;
    }

    public function getPlugins(): array
    {
        $pluginsList = [];
        $plugins = DB::table('plugins')->get(); // connection() is yet running on register(), cannot querying using Model::class

        foreach ($plugins as $plugin) {
            if ($pluginInstance = $this->readPlugin($plugin->path)) {
                $pluginDir = explode('/', $plugin->path);
                array_pop($pluginDir);
                $pluginInfo = $this->aboutPlugin(implode('/', $pluginDir) . '/about.json');
                
                if ($pluginInstance instanceof ClassesPlugin && !in_array($pluginInfo, ['invalid', 'not_found'])) {
                    $pluginsList[$pluginInfo['plugin_name']] = $pluginInstance;
                } else {
                    $pluginsList[$pluginInfo['plugin_name']] = false; // False means invalid
                }
            }
        }

        return $pluginsList;
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

    public function extractPlugin(string $filePath)
    {
        $zip = new ZipArchive();
        $zip->open($filePath);
        $zip->extractTo(storage_path('app/plugins/.temp'));
        $zip->close();

        File::delete($filePath);
    }

    public function pluginInstall(string $file): void
    {
        $pluginZipPath = Storage::disk('plugin-upload');
        $filePath = storage_path("app/plugins/{$file}");
        $temp = storage_path('app/plugins/.temp');

        $this->extractPlugin($filePath);
        $plugin = scandir($temp);

        if ($pluginZipPath->exists("plugins/{$plugin[2]}")) {
            $pluginZipPath->deleteDirectory("plugins/{$plugin[2]}");
        }
        $pluginZipPath->move("/.temp/{$plugin[2]}", base_path("plugins/{$plugin[2]}"));
        File::moveDirectory(storage_path("app/plugins/.temp/{$plugin[2]}"), base_path("plugins/{$plugin[2]}"));

        $currentPlugin = $this->readPlugin("plugins/{$plugin[2]}/index.php");
        $pluginInfo = $this->aboutPlugin("plugins/{$plugin[2]}/about.json");

        if (!in_array($currentPlugin, ['invalid', 'PluginName_not_found', 'not_found'])) {
            if (!in_array($pluginInfo, ['invalid', 'not_found'])) {
                ModelsPlugin::updateOrCreate([
                    'name' => $pluginInfo['plugin_name'],
                    'author' => $pluginInfo['author'],
                ],
                [
                    'description' => $pluginInfo['description'],
                    'version' => $pluginInfo['version'],
                    'path' => "plugins/{$plugin[2]}/index.php",
                    'is_active' => false,
                ]);

                $currentPlugin->onInstall();
                return;
            } else if ($pluginInfo == 'invalid') {
                Notification::make()
                    ->title('Failed to install')
                    ->body('about.json is not valid, please refers to Documentation')
                    ->danger()
                    ->send();
            } else {
                Notification::make()
                    ->title('Failed to install')
                    ->body('about.json is not found')
                    ->danger()
                    ->send();
            }
        } else if ($currentPlugin == 'PluginName_not_found') {
            Notification::make()
                ->title('Failed to install')
                ->body("{$plugin[2]}.php is not found")
                ->danger()
                ->send();
        } else if ($currentPlugin == 'invalid') {
            Notification::make()
                ->title('Failed to install')
                ->body("index.php must return {$plugin[2]}.php instance and {$plugin[2]}.php must be an instanceof App\\Classes\\Plugin")
                ->danger()
                ->send();
        } else {
            Notification::make()
                ->title('Failed to install')
                ->body('index.php is not found')
                ->danger()
                ->send();
        }

        File::deleteDirectory(base_path("plugins/{$plugin[2]}"));
    }

    public function pluginUninstall(ModelsPlugin $record): void
    {
        $plugin = $this->readPlugin($record->path);
        $plugin->onUninstall();

        $bruh = explode('/', $record->path);
        array_shift($bruh);
        $pluginPath = implode('/', $bruh);
        $pluginDir = dirname($pluginPath);

        Storage::disk('plugin-directory')->deleteDirectory($pluginDir);
        $record->delete();
    }

    public function runPlugins(): void
    {
        $plugins = DB::table('plugins')->where('is_active', true)->get(); // connection() is yet running on register(), so yeah.

        foreach ($plugins as $plugin) {
            if ($pluginInstance = $this->readPlugin($plugin->path)) {
                $pluginDir = explode('/', $plugin->path);
                array_pop($pluginDir);
                $pluginInfo = $this->aboutPlugin(implode('/', $pluginDir) . '/about.json');
                
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
        if (file_exists($file = base_path($pluginPath))) {
            try {
                $currentPlugin = include $file;
            } catch (\Throwable $th) {
                return 'PluginName_not_found';
            }
            if ($currentPlugin instanceof ClassesPlugin) {
                return $currentPlugin;
            }
            return 'invalid';
        } else {
            return 'not_found';
        }
    }

    public function aboutPlugin(string $jsonPath)
    {
        $validValues = ['plugin_name', 'author', 'description', 'version'];

        if (file_exists($file = base_path($jsonPath))) {
            $about = File::json($file);
            foreach ($validValues as $validValue) {
                if (!array_key_exists($validValue, $about)) {
                    return 'invalid';
                }
            }
            return $about;
        } else {
            return 'not_found';
        }
    }

    public function scanPlugins()
    {
        $pluginsList = array_diff(scandir(base_path('plugins')), ['.', '..', '.temp']);

        foreach ($pluginsList as $plugin) {
            $about = $this->aboutPlugin("plugins/{$plugin}/about.json");
            ModelsPlugin::updateOrCreate([
                'name' => $about['plugin_name'],
                'author' => $about['author'],
            ],
            [
                'description' => $about['description'],
                'version' => $about['version'],
                'path' => "plugins/{$plugin}/index.php",
                'is_active' => false,
            ]);
        }

        Notification::make()
            ->title('Successfully scanned')
            ->body('New plugins should be now listed')
            ->success()
            ->send();
    }
}
