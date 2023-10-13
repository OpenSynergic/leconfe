# Meta Tag

Meta Tag is a facade class that allows to easily manage html meta tags that will show up in the `<head>` section of the html response.

## How to use

### Add meta tag

```php
use App\Facades\MetaTag;

MetaTag::add('description', 'This is a description');
```

### Remove meta tag

```php
use App\Facades\MetaTag;

MetaTag::remove('description');
```

### Get meta tag

```php
use App\Facades\MetaTag;

MetaTag::get('description');
```

### Get all meta tags

```php
use App\Facades\MetaTag;

MetaTag::getAll();
```

### Render meta tags to html

```php
use App\Facades\MetaTag;

MetaTag::render();
```

Result: 

```html
<meta name="description" content="This is a description">
```