<?php

namespace Plugins\DummyWithComposer;

use App\Classes\Plugin;
use App\Interfaces\HasPlugin;

class DummyWithComposer extends Plugin
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
}