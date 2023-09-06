# Vertical Tabs


## Normal Vertical Tab
```php
use App\Forms\Components\VerticalTabs;
...

public function setupForm(Form $form): Form
    {
        return $form->schema([
                VerticalTabs\Tabs::make()
                    ->tabs([
                        VerticalTabs\Tab::make("User")
                            ->schema([
                                FormSection::make('Profile')
                                    ->schema([
                                        TextInput::make('name')
                                    ])
                                ])
                        ])
                ])
    }
```

## Sticky Vertical Tabs

To make vertical tabs sticky, simply use the `sticky()` method.

```php
 VerticalTabs\Tabs::make()
    ->sticky()
```