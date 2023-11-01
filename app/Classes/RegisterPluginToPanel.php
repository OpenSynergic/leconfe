<?php
 
namespace App\Classes;

use App\Facades\Plugin as FacadesPlugin;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Illuminate\Support\Facades\Config;

class RegisterPluginToPanel implements Plugin
{
    public function getId(): string
    {
        return 'RegisterPluginToPanel';
    }
 
    public function register(Panel $panel): void
    {
        //
    }
 
    public function boot(Panel $panel): void
    {
        //
    }
}