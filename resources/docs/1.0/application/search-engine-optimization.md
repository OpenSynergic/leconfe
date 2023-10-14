# SEO (Search Engine Optimization)

This section will be covering about [Leconfe](https://github.com/OpenSynergic/leconfe) Search Engine Optimization

## Sitemap
Sitemap are generated using [spatie/laravel-sitemap](https://github.com/spatie/laravel-sitemap) and accessible at `your-domain.com/sitemap`

### Adding custom path
Custom path are added through `generate-sitemap` route in `web.php`.
```php
Route::get('/sitemap', function () {
    return Sitemap::create()
        ...
        ->add('/your-custom-pathname')
})->name('generate-sitemap');
```

### Automatically adding path with dynamic parameter
To automatically add paths with parameter that dynamically filled with `id` or `slug` you can implement `Spatie\Sitemap\Contracts\Sitemapable` to your model and add `toSitemapTag()` that returns your route with your model as argument.
```php
use Spatie\Sitemap\Contracts\Sitemapable;
use Spatie\Sitemap\Tags\Url;

class YourModel extends Model implements Sitemapable
{
    public function toSitemapTag(): Url | string | array
    {
        return route('route-name', $this);
    }
}
```
Then you can add it by passing `Illuminate\Database\Eloquent\Collection` of your model.
```php
Route::get('/sitemap', function () {
    return Sitemap::create()
        ...
        ->add(YourModel::all())
})->name('generate-sitemap');
```