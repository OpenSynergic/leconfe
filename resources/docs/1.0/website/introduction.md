# Introduction

---

Utilizing the power of [Livewire Page Group](https://github.com/rahmanramsi/livewire-page-group), this package provides a simple way to create a pages for Conference Application.

## Folder Structure

```base
└── app
    └── Website
       ├── Pages
       └── Livewire
```

## Create a Page
To create a page, you can use the following command:

```bash
php artisan make:livewire-page
```

And follow the instructions.

This command will create a page in `app/Website/Pages` folder.

## Route
Routing is automatically handled by the package. You don't need to add any route manually.

### Custom Route
If you want to custom a route, you can override the `routes` method in page class.

```php

use Rahmanramsi\LivewirePageGroup\PageGroup;

public static function routes(PageGroup $pageGroup): void
{
    $slug = static::getSlug();
    Route::get("/{$slug}", static::class)
        ->middleware(static::getRouteMiddleware($pageGroup))
        ->withoutMiddleware(static::getWithoutRouteMiddleware($pageGroup))
        ->name((string) str($slug)->replace('/', '.'));
}
```

