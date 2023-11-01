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
### Plugin information
A detailed information about the plugin are stored in `plugin.json`.
```json
{
    "plugin_name": "Plugin Name",
    "author": "Author Name",
    "description": "Description about the plugin",
    "version": "1.0"
}
```
## Plugin Actions
Plugin actions are core functions to the plugin and are located at `pluginName.php`.

```php
...

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

...
```