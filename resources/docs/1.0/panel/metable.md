# Metable

Metable is a trait that allows you to add meta data to your models. It is used by the `Meta` class to store and retrieve meta data.

## How to use

[plank/laravel-metable](https://github.com/plank/laravel-metable)

## Use enum on metable

### Add Enum handler to Metable

WIP

### Form

```php
 Radio::make('meta.status')
    ->enum(SubmissionStatus::class)
    ->options(SubmissionStatus::array())
    ->dehydrateStateUsing(fn (string $state): SubmissionStatus => SubmissionStatus::from($state)),
```

### Table

WIP
