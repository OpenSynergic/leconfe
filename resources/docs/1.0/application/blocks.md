# Overview

A block is a section of the page displayed on the front page. It can be found on the sidebar, either on the left, right, or both sides.

# How to create

To create a block, you need to create a class that extends the `\App\Classes\Block` class. The class must be located in the `app/Website/Blocks` directory.

The class must have the following properties:

1. `$view` - The view that will be rendered when the block is displayed. 
2. `$sort` - The sort order of the block. The lower the number, the higher the block will be displayed.
3. `$position` - The position of the block. It can be `left`, `right`. If it is null, the block will not be displayed.


> {primary} If you make a setting in the admin panel Setting section (Conference -> Appearance -> Sidebar), the values from the database will override those properties.


for example you can look at the following code:

```php
namespace App\Website\Blocks;

use App\Classes\Block;
use Illuminate\Contracts\View\View;

class ExampleBlock extends Block
{
    protected string | null $view = 'website.blocks.example-block';

    protected int | null $sort = 1;

    protected string | null $position = 'right';
}
```