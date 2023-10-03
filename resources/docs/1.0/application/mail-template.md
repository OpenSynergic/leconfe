# Mail Templates
All email that send from this application is using mail template. Mail template is a Template that can be customized by user. This section will explain how to create mail template.
This mail template is created based on the [Laravel Database Mail Templates](https://github.com/spatie/laravel-database-mail-templates) from [Spatie](https://spatie.be/).

## Creating A Mail Template
Every email template have their own class. The class is located in `app/Mail/Templates` directory. The class must extends `App\Mail\Templates\TemplateMailable` class.

```php
<?php

namespace App\Mail\Templates;

class WelcomeMail extends TemplateMailable
{
    public $name;

    public function __construct(User $user)
    {
        $this->name = $user->full_name;
    }
}

```



The class must have the following method:

1. The default subject of the email. The method must return a string.

```php
public static function getDefaultSubject(): string
{
    return 'Welcome, {{ name }}';
}
```

2. The default description of the email. The method must return a string.
   
```php
public static function getDefaultDescription(): string
{
    return 'Description of the template';
}    
```

3. The default html template of the email. The method must return a string.

```php
public static function getDefaultHtmlTemplate(): string
{
    return <<<'HTML'
    <p>Hello, {{ name }}.</p>
    HTML;
}
```
As you can see in the above example, you can use [mustache template](http://mustache.github.io/) tags in both the subject and body of the mail template!

## Sending email using mail template
Just like sending email using Laravel, you can use `Mail` facade to send email using mail template. The only difference is you need to pass the mail template class name to the `send` method.

```php
Mail::to($user)->send(new WelcomeMail($user));
```