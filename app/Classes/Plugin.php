<?php

namespace App\Classes;

use App\Facades\Plugin as FacadesPlugin;
use App\Interfaces\HasPlugin;
use App\Models\Plugin as ModelsPlugin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use ReflectionClass;

abstract class Plugin implements HasPlugin
{
    public function boot()
    {
        // Stage is yours
    }

    public function onActivation()
    {
        // Runs on plugin activation
    }

    public function onDeactivation()
    {
        // Runs on plugin deactivation
    }

    public function onInstall()
    {
        // Runs on plugin installation
    }

    public function onUninstall()
    {
        // Runs on plugin uninstallation
    }

    public function getPluginPath()
    {
        $pluginDir = new ReflectionClass($this);
        return $pluginDir->getFileName();
    }
}