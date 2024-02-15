<?php

namespace App\Classes;

use App\Facades\Plugin as FacadesPlugin;
use App\Interfaces\HasPlugin;
use Filament\Panel;
use Illuminate\Support\Facades\View;
use Symfony\Component\Yaml\Yaml;

abstract class Plugin implements HasPlugin
{
    protected array $info;

    protected string $pluginPath;

    public function load(): void
    {
        $this->info = Yaml::parseFile($this->getPluginInformationPath());

        View::addNamespace($this->getInfo('folder'), $this->getPluginPath() . DIRECTORY_SEPARATOR . 'views');
    }
    
    public function getInfo(?string $key = null)
    {   
        if ($key) {
            return $this->info[$key] ?? null;
        }

        return $this->info;
    }

    public function getPluginPath()
    {
        return $this->pluginPath;
    }

    public function getPluginInformationPath()
    {
        return $this->getPluginPath() . DIRECTORY_SEPARATOR . 'index.yaml';
    }

    public function setPluginPath($path): void
    {
        $this->pluginPath = $path;
    }

    public function getSetting($key): mixed
    {
        return FacadesPlugin::getSetting($this->getPluginPath(), $key);
    }
    
    public function onPanel(Panel $panel): void
    {
        // Implement this method to add your plugin to the panel
    }

    public function onAdministrationPanel(Panel $panel): void
    {
        // Implement this method to add your plugin to the panel administration
    }
}
