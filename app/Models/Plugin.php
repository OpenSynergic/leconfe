<?php

namespace App\Models;

use App\Facades\Plugin as FacadesPlugin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Sushi\Sushi;

class Plugin extends Model
{
    use Sushi;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $schema = [
        'id' => 'integer',
        'plugin_name' => 'string',
        'author' => 'string',
        'description' => 'string',
        'version' => 'string'
    ];

    public function getRows()
    {
        return FacadesPlugin::getRegisteredPlugins()
            ->map(function ($pluginInfo, $pluginDir) {
                $pluginInfo['id'] = $pluginDir;

                return $pluginInfo;
            })
            ->values()
            ->toArray();
    }

    protected function sushiShouldCache()
    {
        return false;
    }

    public function settings() : HasMany
    {
        return $this->hasMany(PluginSetting::class, 'plugin', 'dir');
    }
}
