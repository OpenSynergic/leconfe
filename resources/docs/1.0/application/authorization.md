# Authorization

Leconfe uses [Laravel-permission](https://spatie.be/docs/laravel-permission/v5/introduction) package to handle authorization.

Because all permissions will be registered on [Laravel's gate](https://laravel.com/docs/authorization), you can check if a user has a permission with Laravel's default can function:

```php
$user->can('update', $conference);
```
and Blade directives:

```blade
@can('update', $conference)
    . . .
@endcan
```

For more information on how to use Laravel's authorization features, check out [the documentation](https://spatie.be/docs/laravel-permission/v5/basic-usage/basic-usage).

## Best Practices
All permission that are related to a model should be registered in the model's policy, and using the following naming convention: `ModelName:actionName`.

For example : `Conference:update`.

Outside of the model you should use the following naming convention: `contextName:actionName`.

