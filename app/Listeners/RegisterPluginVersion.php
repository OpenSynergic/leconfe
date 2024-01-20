<?php

namespace App\Listeners;

use App\Events\PluginInstalled;
use App\Models\Version;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Symfony\Component\Yaml\Yaml;

class RegisterPluginVersion
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PluginInstalled $event): void
    {
        $plugin = $event->plugin;

        // Parse plugin index.yaml
        $pluginIndex = Yaml::parseFile($plugin->getPluginFullPath() . DIRECTORY_SEPARATOR . 'index.yaml');

        // Save plugin version
        Version::firstOrCreate([
            'product_name' => $pluginIndex['name'],
            'product_folder' => $pluginIndex['folder'],
            'version' => $pluginIndex['version'],
        ], [
            'installed_at' => now(),
        ]);
    }
}
