Async Queue driver for Laravel
======================
Async Queue driver for Laravel provides an asyncronous queue processing without using any third-party queue services like Redis, RabbitMQ, etc.

Under the hood, it uses [AMPHP](https://amphp.org/) to handle asyncronous queue processing. All jobs are processed after the response is sent to the browser.


## Installation

You can install this package via composer using this command:

```
composer require rahmanramsi/laravel-async-queue
```

The package will automatically register itself.

### Configuration

Add connection to `config/queue.php`:

> This is the config for the async connection/driver to work.

```php
'connections' => [
    // ...
    'async' => [
        'driver' => 'async',
    ],

    // ...    
],
```

## Usage
Change the default queue driver to `async` in your `.env` file:

```env
QUEUE_CONNECTION=async
```

If you do not know how to use the Queue API, please refer to the official Laravel documentation: http://laravel.com/docs/queues

## Contribution

You can contribute to this package by discovering bugs and opening issues. Please, add to which version of package you
create pull request or issue. (e.g. [5.2] Fatal error on delayed job)