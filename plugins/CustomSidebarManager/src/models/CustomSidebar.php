<?php

namespace CustomSidebarManager\Models;

use App\Facades\Plugin;
use Illuminate\Database\Eloquent\Model;
use Sushi\Sushi;

class CustomSidebar extends Model
{
    use Sushi;

    public $incrementing = false;

    // protected $primaryKey = 'name';

    protected $keyType = 'string';

    protected $schema = [
        'id' => 'integer',
        'name' => 'string',
        'show_name' => 'boolean',
        'content' => 'string',
    ];

    public function getRows()
    {
        $plugin = Plugin::getPlugin('CustomSidebarManager');
        // dd($plugin->getSetting('blocks', []));

        return $plugin->getSetting('blocks', []);
    }

    protected function sushiShouldCache()
    {
        return false;
    }
}
