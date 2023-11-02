# Plugin API
Plugin API are used to set your plugin at runtime.

## Core
These are Core actions to the plugin that are located at `pluginName.php`.

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