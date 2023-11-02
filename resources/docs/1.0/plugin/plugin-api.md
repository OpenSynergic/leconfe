# Plugin API
Plugin API are used to set your plugin at runtime.

## Core functions
These are essential plugin functions that runs at specified runtimes and act as the designated space for integrating code to extend leconfe. Located at `PluginName.php`.
### `boot()`
Executes when the plugin is activated.
```php
...

public function boot()
{
    //
}

...
```
### `onActivation()`
Executes on plugin activation.
```php
...

public function onActivation()
{
    // 
}

...
```
### `onDeactivation()`
Executes on plugin deactivation.
```php
...

public function onDeactivation()
{
    // 
}

...
```
### `onInstall()`
Executes on plugin install.
```php
...

public function onInstall()
{
    // 
}

...
```
### `onUninstall()`
Executes on plugin Uninstall.
```php
...

public function onUninstall()
{
    // 
}

...
```