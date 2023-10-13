# SEO (Search Engine Optimization)

This section will be covering about [Leconfe](https://github.com/OpenSynergic/leconfe) Search Engine Optimization

## Adding custom path to sitemap
Sitemap are generated using [spatie/laravel-sitemap](https://github.com/spatie/laravel-sitemap) and custom path are added through `generate-sitemap` route in `web.php`.
```php
Route::get('/sitemap', function () {
    return Sitemap::create()
        ...
        ->add('/your-custom-pathname')
})->name('generate-sitemap');
```