<?php

namespace Plugins\Dummy;

use App\Classes\Plugin;
use App\Facades\Plugin as FacadesPlugin;
use App\Models\Plugin as ModelsPlugin;
use App\Models\Timeline;
use Illuminate\Support\Facades\File;

class Dummy extends Plugin
{
    public function boot()
    {
        // Stage is yours

        // dd(FacadesPlugin::getPlugins());
    }

    public function onActivation()
    {
        // Runs on plugin activation
        $about = $this->pluginInfo(dirname($this->getPluginPath()) . '/about.json');

        $bruh = ModelsPlugin::where('name', $about['plugin_name'])->first();
        $bruh->description = 'this description is added on activation';
        $bruh->save();
    }

    public function onDeactivation()
    {
        // Runs on plugin deactivation
        $about = $this->pluginInfo(dirname($this->getPluginPath()) . '/about.json');

        $bruh = ModelsPlugin::where('name', $about['plugin_name'])->first();
        $bruh->description = 'this description is added on deactivation';
        $bruh->save();
    }

    public function onInstall()
    {
        // Runs on plugin installation
        $about = $this->pluginInfo(dirname($this->getPluginPath()) . '/about.json');

        $bruh = ModelsPlugin::where('name', $about['plugin_name'])->first();
        $bruh->description = 'this description is added on install';
        $bruh->save();
    }

    public function onUninstall()
    {
        // Runs on plugin uninstallation
    }

    public function pluginInfo(string $filePath) // user custom method example
    {
        return File::json($filePath);
    }
}