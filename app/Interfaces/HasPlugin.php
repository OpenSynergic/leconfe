<?php

namespace App\Interfaces;

interface HasPlugin
{
    public function boot();
    public function onActivation();
    public function onDeactivation();
    public function onInstall();
    public function onUninstall();
}