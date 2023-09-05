# Authorization

## Introduction
Authorization refers to the process of determining whether a user, typically an authenticated user, has the necessary permissions to perform a specific action or access a particular resource within Leconfe. Laravel (framework that this application uses) provides a powerful and flexible authorization system that makes it easy to implement access control in Leconfe.

Leconfe also uses [Laravel-permission](https://spatie.be/docs/laravel-permission/v5/introduction) package to handle dynamic permissions.

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
## Creating Policies
### Generating Policies
Policies are classes that organize authorization logic around a particular model or resource. For example, if your application is a blog, you may have a `App\Models\Post` model and a corresponding `App\Policies\PostPolicy` to authorize user actions such as creating or updating posts.

You may generate a policy using the `make:policy` Artisan command. The generated policy will be placed in the `app/Policies` directory :
```bash
php artisan make:policy PostPolicy --model=Post
```

### Writing Policies
Once the policy class has been registered, you may add methods for each action it authorizes. For example, let's define an `update` method on our `PostPolicy` which determines if a given `App\Models\User` can update a given `App\Models\Post` instance.

The `update` method will receive a `User` and a `Post` instance as its arguments, and should return `true` or `false` indicating whether the user is authorized to update the given Post. So, in this example, we will verify that the user is authorized to update a given post by checking if user has the `User:update` permission
```php
<?php
 
namespace App\Policies;
 
use App\Models\Post;
use App\Models\User;
 
class PostPolicy
{
    /**
     * Determine if the given post can be updated by the user.
     */
    public function update(User $user, Post $post)
    {
        if($user->can('User:update')) return true;
    }
}
```
Note that we only return `true` when user is authorized to update the post. And not returning anything because this will be useful in some edge case. You can read blog post about this [here](https://freek.dev/1325-when-to-use-gateafter-in-laravel).


## Best Practices
All permission that are related to a model should be registered in the model's policy, and using the following naming convention: `ModelName:actionName`.

For example : `Conference:update`.

Outside of the model you should use the following naming convention: `contextName:actionName`.

