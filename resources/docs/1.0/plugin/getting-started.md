# Getting Started

## Generating Plugin
Plugin is generated through command-line interface (CLI).
```base
php artisan make:plugin {PluginName} {Author}
```

## Plugin Structure
### Folder Structure
Plugins are located in `/plugins` folder at the root of application. A correct plugin must contain and structured as example below
```base
└── pluginName/
    ├── about.json
    ├── index.php
    └── pluginName.php
```
### about.json
A detailed information about the plugin.
```json
{
    "plugin_name": "Plugin Name",
    "author": "Author Name",
    "description": "Description about the plugin",
    "version": "1.0"
}
```
### index.php
The `index.php` is the main entry point of plugin that must return `Plugins\PluginName\PluginName` instance.
```php
use Plugins\PluginName\PluginName;

return new PluginName();
```
### PluginName.php
Contains core actions to the plugin at runtime.
```php
namespace Plugins\DummyWithComposer;

use App\Classes\Plugin;

class PluginName extends Plugin
{
    public function boot()
    {
        // Runs when plugin has been activated
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
```