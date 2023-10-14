# Shortcode
Shortcode are added using [webwizo/laravel-shortcodes](https://github.com/webwizo/laravel-shortcodes)

## Enabling shortcode functionality
Shortcode are enabled per-page. To enable shortcode functionality you can add `Shortcode::enabled()` within your page initialization.
```php
use Rahmanramsi\LivewirePageGroup\Pages\Page;
use Webwizo\Shortcodes\Facades\Shortcode;

class YourPage extends Page
{
    ...

    public function mount()
    {
        Shortcode::enable();
    }

    ...
}
```

## Adding shortcode
Shortcode can be added by creating a class in `App\Shortcodes`.
```php
namespace App\Shortcodes;

class YourShortcode {

    public function register($shortcode, $content, $compiler, $name, $viewData)
    {
        return "<a {$shortcode->get('attribute-name', 'default-value')}>{$content}</a>";
    }
  
}
```
Then register your shortcode in `App\Providers\ShortcodesServiceProvider;`
```php
use App\Shortcodes\YourShortcode;
use Illuminate\Support\ServiceProvider;
use Webwizo\Shortcodes\Facades\Shortcode;

class ShortcodesServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        Shortcode::register('your-shortcode', YourShortcode::class);
    }

    ...
}
```

## Using shortcode
```html
[your-shortcode attribute="attribute-value"]Shortcode[/your-shortcode]
```