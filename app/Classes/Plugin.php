<?php

namespace App\Classes;

use App\Facades\Plugin as FacadesPlugin;
use App\Interfaces\HasPlugin;

abstract class Plugin implements HasPlugin
{
    protected string $pluginPath;

    public function getPluginPath()
    {
        return $this->pluginPath;
    }

    public function getPluginFullPath()
    {
        return FacadesPlugin::getDisk()->path($this->getPluginPath());
    }

    public function setPluginPath($path): void
    {
        $this->pluginPath = $path;
    }

    public function getSetting($key): mixed
    {
        return FacadesPlugin::getSetting($this->getPluginPath(), $key);
    }
}
