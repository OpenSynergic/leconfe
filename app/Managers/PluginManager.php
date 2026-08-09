<?php

namespace App\Managers;

use App\Classes\DefaultTheme;
use App\Classes\ManualPaymentPlugin;
use App\Classes\Plugin as ClassesPlugin;
use App\Events\PluginInstalled;
use App\Models\PluginSetting;
use App\Support\FrankenPhpWorkerReloader;
use Exception;
use Illuminate\Contracts\Filesystem\Filesystem as FilesystemContract;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Yaml\Yaml;
use Throwable;
use ZipArchive;

class PluginManager
{
    protected Collection $plugins;

    protected bool $isBooted = false;

    protected ?array $initializedContext = null;

    protected array $terminatingPluginMutations = [];

    protected bool $pluginTerminationScheduled = false;

    public function __construct()
    {
        $this->plugins = collect();
    }

    public function getCurrentContextString(): string
    {
        if (app()->isOnScheduledConference()) {
            return 'scheduled-conference';
        }

        if (app()->isOnConference()) {
            return 'conference';
        }

        return 'site';
    }

    public function initialize()
    {
        // TODO Add support for plugin in console
        if (app()->runningInConsole()) {
            return;
        }

        if (! app()->isInstalled()) {
            return;
        }

        $disk = $this->getDisk();

        $context = $this->getCurrentContextString();

        $this->pluginDirectoriesForContext($context)
            ->each(function ($pluginPath) use ($disk) {
                $plugin = $this->initiatePlugin($disk->path($pluginPath));

                $this->register($pluginPath, $plugin, $this->getSetting($plugin, 'enabled', false));
            });

        $this->initializedContext = $this->currentContext();
    }

    protected function pluginDirectoriesForContext(string $context): Collection
    {
        $disk = $this->getDisk();

        return collect($disk->directories())
            ->filter(function ($pluginDir) use ($disk, $context) {
                try {
                    if (Str::contains($pluginDir, ' ')) {
                        throw new Exception("Plugin folder name ({$pluginDir}) cannot contain spaces");
                    }

                    if (! $disk->exists($pluginDir.DIRECTORY_SEPARATOR.'index.yaml')) {
                        throw new Exception("Plugin ({$pluginDir}) is missing index.yaml file");
                    }

                    if (! $disk->exists($pluginDir.DIRECTORY_SEPARATOR.'index.php')) {
                        throw new Exception("Plugin ({$pluginDir}) is missing index.php file");
                    }
                } catch (Throwable $th) {
                    return false;
                }

                $informations = Yaml::parseFile($disk->path($pluginDir.DIRECTORY_SEPARATOR.'index.yaml'));
                $targets = Arr::get($informations, 'targets');
                $sitewide = Arr::get($informations, 'sitewide', false);

                if ($sitewide) {
                    return true;
                }

                if (! empty($targets) && ! in_array($context, $targets)) {
                    return false;
                }

                return true;
            });
    }

    public function getPluginsForRegistration(string $context): Collection
    {
        if (! isset($_SERVER['LARAVEL_OCTANE'])) {
            return $this->getPlugins();
        }

        $plugins = $this->plugins->only(['DefaultTheme', 'ManualPayment']);

        if (! app()->isInstalled()) {
            return $plugins;
        }

        $disk = $this->getDisk();

        $this->pluginDirectoriesForContext($context)->each(function ($pluginPath) use ($disk, $plugins) {
            $plugins->put($pluginPath, $this->initiatePlugin($disk->path($pluginPath))->load());
        });

        return $plugins;
    }

    public function reinitialize()
    {
        $this->plugins = collect();
        $this->registerCorePlugins();
        $this->initialize();
    }

    public function registerCorePlugins(): void
    {
        if (! $this->plugins->has('DefaultTheme')) {
            $this->register('DefaultTheme', new DefaultTheme, true);
        }

        if (! $this->plugins->has('ManualPayment')) {
            $this->register('ManualPayment', new ManualPaymentPlugin, true);
        }
    }

    public function ensureCurrentContextInitialized(): void
    {
        if ($this->initializedContext !== $this->currentContext()) {
            $this->reinitialize();
        }
    }

    protected function currentContext(): array
    {
        return [
            App::getCurrentConferenceId(),
            App::getCurrentScheduledConferenceId(),
        ];
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

    public function register(string $id, ClassesPlugin $plugin, bool $boot = false)
    {
        try {
            $plugin->load();

            if ($boot) {
                $plugin->bootPlugin();
            }

            $this->plugins->put($id, $plugin);
        } catch (Throwable $th) {
            throw $th;
        }
    }

    protected function initiatePlugin(string $pluginPath): ?ClassesPlugin
    {
        $plugin = include $pluginPath.DIRECTORY_SEPARATOR.'index.php';

        if (! $plugin instanceof ClassesPlugin) {
            throw new Exception('Plugin must return an instance of '.ClassesPlugin::class);
        }

        return $plugin->setPluginPath($pluginPath);
    }

    public function getPlugins(bool $onlyEnabled = true)
    {
        return $this->plugins->when($onlyEnabled, fn ($plugins) => $plugins->filter(fn ($plugin) => $plugin->isEnabled()));
    }

    public function getPlugin(?string $path, bool $onlyEnabled = false): ?ClassesPlugin
    {
        return $this->getPlugins($onlyEnabled)->get($path);
    }

    protected function getCacheKey($plugin, $key, $conferenceId = null, $scheduledConferenceId = null)
    {
        $conferenceId = $conferenceId ?? App::getCurrentConferenceId();
        $scheduledConferenceId = $scheduledConferenceId ?? App::getCurrentScheduledConferenceId();

        return md5(implode('_', [
            'plugin_setting',
            $conferenceId,
            $scheduledConferenceId,
            $plugin,
            $key,
        ]));
    }

    protected function getPluginFolder(ClassesPlugin $plugin): string
    {
        return $plugin->getInfo('folder');
    }

    protected function isPluginSitewide(ClassesPlugin $plugin): bool
    {
        return $plugin->getInfo('sitewide') ?? false;
    }

    public function getSetting(ClassesPlugin $plugin, mixed $key, $default = null): mixed
    {
        $pluginFolder = $this->getPluginFolder($plugin);
        $sitewide = $this->isPluginSitewide($plugin);

        if ($sitewide) {
            $conferenceId = 0;
            $scheduledConferenceId = 0;
        } else {
            $conferenceId = App::getCurrentConferenceId();
            $scheduledConferenceId = App::getCurrentScheduledConferenceId() ?? 0;
        }

        return Cache::rememberForever($this->getCacheKey($pluginFolder, $key, $conferenceId, $scheduledConferenceId), function () use ($pluginFolder, $key, $default, $conferenceId, $scheduledConferenceId) {
            $setting = PluginSetting::query()
                ->where('conference_id', $conferenceId)
                ->where('scheduled_conference_id', $scheduledConferenceId)
                ->where('plugin', $pluginFolder)
                ->where('key', $key)
                ->first();

            return $setting ? $this->convertFromDB($setting->value, $setting->type) : $default;
        });
    }

    public function updateSetting(ClassesPlugin $plugin, $key, $value): mixed
    {
        $pluginFolder = $this->getPluginFolder($plugin);
        $sitewide = $this->isPluginSitewide($plugin);

        if ($sitewide) {
            $conferenceId = 0;
            $scheduledConferenceId = 0;
        } else {
            $conferenceId = App::getCurrentConferenceId();
            $scheduledConferenceId = App::getCurrentScheduledConferenceId() ?? 0;
        }

        Cache::forget($this->getCacheKey($pluginFolder, $key, $conferenceId, $scheduledConferenceId));

        $type = $this->getType($value);

        return PluginSetting::query()
            ->updateOrInsert(
                [
                    'plugin' => $pluginFolder,
                    'conference_id' => $conferenceId,
                    'scheduled_conference_id' => $scheduledConferenceId,
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
        File::cleanDirectory($this->getTempDisk()->path(''));
    }

    public function applyFilament5CompatibilityFix(string $pluginPath): void
    {
        if (! is_dir($pluginPath)) {
            return;
        }

        $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($pluginPath));
        foreach ($it as $file) {
            if ($file->isDir() || $file->getExtension() !== 'php') {
                continue;
            }

            $filepath = $file->getPathname();
            $content = file_get_contents($filepath);
            $original = $content;

            // 1. Replace "protected static string $view =" or similar for Filament Pages only
            if (str_contains($content, 'use Filament\Pages\Page;')) {
                $content = preg_replace(
                    '/protected\s+static\s+(string|\?string)?\s*\$view\s*=/',
                    'protected string $view =',
                    $content
                );
            }

            // 2. Replace "protected static ?string $navigationGroup ="
            $content = preg_replace(
                '/protected\s+static\s+\?string\s+\$navigationGroup\s*=/',
                'protected static string | \UnitEnum | null $navigationGroup =',
                $content
            );

            // 3. Replace "protected static ?string $navigationIcon ="
            $content = preg_replace(
                '/protected\s+static\s+\?string\s+\$navigationIcon\s*=/',
                'protected static string | \BackedEnum | null $navigationIcon =',
                $content
            );

            // 4. Replace "use Filament\Tables\Actions\ActionGroup;"
            $content = str_replace(
                'use Filament\Tables\Actions\ActionGroup;',
                'use Filament\Actions\ActionGroup;',
                $content
            );

            // 5. Replace "use Filament\Tables\Actions\Action as TableAction;"
            $content = str_replace(
                'use Filament\Tables\Actions\Action as TableAction;',
                'use Filament\Actions\Action as TableAction;',
                $content
            );

            // 6. Update routes method signature and body in page classes
            if (str_contains($content, 'public static function routes(Panel $panel)')) {
                $content = str_replace(
                    'public static function routes(Panel $panel): void',
                    'public static function routes(Panel $panel, ?\Filament\Pages\PageConfiguration $configuration = null): void',
                    $content
                );
                $content = str_replace(
                    'static::getRoutePath()',
                    'static::getRoutePath($panel)',
                    $content
                );
                $content = str_replace(
                    'static::getRelativeRouteName()',
                    'static::getRelativeRouteName($panel)',
                    $content
                );
            }

            // 7. Update getRoutePath signature in page classes
            if (str_contains($content, 'public static function getRoutePath(): string')) {
                $content = str_replace(
                    'public static function getRoutePath(): string',
                    'public static function getRoutePath(\Filament\Panel $panel): string',
                    $content
                );
            }

            if ($content !== $original) {
                file_put_contents($filepath, $content);
            }
        }
    }

    public function install(string $file)
    {
        $pluginTempDisk = $this->getTempDisk();

        if (! $folderName = $this->extractToTempPlugin($file)) {
            throw new Exception('Cannot extract the plugin, please check the zip file');
        }

        $this->validatePlugin($pluginTempDisk->path($folderName));

        $fileSystem = new Filesystem;
        $fileSystem->copyDirectory($pluginTempDisk->path($folderName), $this->getDisk()->path($folderName));
        $this->applyFilament5CompatibilityFix($this->getDisk()->path($folderName));
        $this->cleanTempPlugins();

        try {
            $plugin = $this->initiatePlugin($this->getDisk()->path($folderName), true);
        } catch (Throwable $th) {
            $pluginTempDisk->deleteDirectory($folderName);

            throw $th;
        }

        PluginInstalled::dispatch($plugin);

        $this->reinitialize();
        $this->schedulePluginTermination();

        return true;
    }

    public function validatePlugin(string $pluginPath)
    {
        if (! file_exists($pluginPath)) {
            throw new Exception("Plugin {$pluginPath} not found");
        }

        $pluginName = basename($pluginPath);

        if (Str::contains($pluginPath, ' ')) {
            throw new Exception("Plugin folder name ({$pluginName}) cannot contain spaces");
        }

        if (! file_exists($pluginPath.DIRECTORY_SEPARATOR.'index.yaml')) {
            throw new Exception("Plugin ({$pluginName}) is missing index.yaml file");
        }

        if (! file_exists($pluginPath.DIRECTORY_SEPARATOR.'index.php')) {
            throw new Exception("Plugin ({$pluginName}) is missing index.php file");
        }
    }

    protected function extractToTempPlugin(string $filePath): string
    {
        try {
            if (! class_exists('ZipArchive')) {
                throw new Exception('Please Install PHP Zip Extension');
            }

            if (! file_exists($filePath)) {
                throw new Exception("File {$filePath} not found");
            }

            if (pathinfo($filePath)['extension'] != 'zip') {
                throw new Exception('Plugin extension must be .zip');
            }

            $zip = new ZipArchive;
            if ($zip->open($filePath) !== true) {
                throw new Exception('Cannot open the zip, please check the zip file');
            }

            $pluginInfo = null;

            for ($i = 0; $i < $zip->numFiles; $i++) {
                $filename = $zip->getNameIndex($i);
                if (! Str::contains($filename, 'index.yaml')) {
                    continue;
                }

                $pluginInfo = Yaml::parse($zip->getFromIndex($i));
            }

            if (! $pluginInfo) {
                throw new Exception('Plugin does not contain index.yaml file');
            }

            if (! isset($pluginInfo['name'])) {
                throw new Exception('Plugin must contain a name information in the index.yaml file');
            }

            if (! isset($pluginInfo['folder'])) {
                throw new Exception('Plugin must contain a `folder` information with the same name as the plugin folder name');
            }

            if (! $zip->extractTo($this->getTempDisk()->path(''))) {
                throw new Exception('Cannot extract the zip, please check the zip file');
            }

            $zip->close();

            if (! file_exists($this->getTempDisk()->path($pluginInfo['folder']))) {
                throw new Exception('Plugin must contain a folder with the same name as the plugin folder name');
            }
        } catch (Throwable $th) {
            throw $th;
        }

        return $pluginInfo['folder'];
    }

    public function uninstall(string $pluginPath): void
    {
        $this->schedulePluginTermination(
            fn () => $this->getDisk()->deleteDirectory($pluginPath),
        );
    }

    protected function schedulePluginTermination(?callable $mutation = null): void
    {
        if ($mutation) {
            $this->terminatingPluginMutations[] = $mutation;
        }

        if ($this->pluginTerminationScheduled || (! $mutation && ! isset($_SERVER['LARAVEL_OCTANE']))) {
            return;
        }

        $this->pluginTerminationScheduled = true;

        app()->terminating(function (): void {
            foreach ($this->terminatingPluginMutations as $mutation) {
                $mutation();
            }

            if (isset($_SERVER['LARAVEL_OCTANE'])) {
                app(FrankenPhpWorkerReloader::class)->reload();
            }
        });
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
                    if (! is_numeric($value)) {
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

                return ! is_null($decodedValue) ? $decodedValue : [];
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
}
