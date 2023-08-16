-   [Conference](#conference)
-   [Using This Guide](#using-this-guide)
    <a name="conference"></a>
    <a name="using-this-guide"></a>

# Conference

---

This document describes the coding conventions and concepts that power Conference Application. It is written for software developers who want to fix bugs, add new features, and contribute code.

## Using this guide

This guide will help you learn how to contribute code to the project. It is written as a high-level introduction to the application structure and the most common utilities you will need to begin working with the code.

## Directory Structure

This application is using Laravel 10 as a framework. You can see [Directory Structure](https://laravel.com/docs/10.x/structure) by laravel.

Beside that, there's some additional folder that you need to know:

```base
└── app
    ├── Actions
    ├── Infolists
    ├── Livewire
    └── Models
        └── Enums
    ├── Panel
    ├── Website
    └── Schemas
```

### The `Actions` Directory

The `Actions` directory contains all of the action classes that will be used in the application. These classes are generated using the `php artisan make:action` command. To learn more about actions, check out the [Laravel Actions](https://laravelactions.com/) documentation.

### The `Infolists` Directory

The `Infolists` directory contains all of the Custom [Filament Infolists](https://filamentphp.com/docs/3.x/infolists/getting-started) that will be used in the application.
To create `Entry` component for infolist, you can use `php artisan make:infolist-entry` command.
To create `Layout` component for infolist, you can use `php artisan make:infolist-layout` command.

To learn more about infolists, check out the [Filament Infolists](https://filamentphp.com/docs/3.x/infolists/getting-started) documentation.

### The `Livewire` Directory
The `Livewire` directory contains the [Livewire](https://livewire.laravel.com/docs/quickstart) components that will be used in the application. To create a new Livewire component, you can use the `php artisan make:livewire` command.

### The `Models/Enums` Directory
The `Models/Enums` directory contains all of the PHP enums related to the models.

### The `Panel` Directory
The `Panel` directory contains all of the [Filament](https://filamentphp.com/docs/3.x/getting-started) resources that will be used in the application. This is where all admin panel resources are defined.

### The `Website` Directory
The `Website` directory contains all of the main pages of the website. This is where all website resources are defined.