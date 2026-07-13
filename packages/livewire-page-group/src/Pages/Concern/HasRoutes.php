<?php

namespace Rahmanramsi\LivewirePageGroup\Pages\Concern;

use Illuminate\Support\Facades\Route;
use Rahmanramsi\LivewirePageGroup\Facades\LivewirePageGroup;
use Rahmanramsi\LivewirePageGroup\PageGroup;

trait HasRoutes
{
    protected static ?string $slug = null;

    /**
     * @var string | array<string>
     */
    protected static string|array $routeMiddleware = [];

    /**
     * @var string | array<string>
     */
    protected static string|array $withoutRouteMiddleware = [];

    public static function routes(PageGroup $pageGroup): void
    {
        $slug = static::getSlug();
        Route::get("/{$slug}", static::class)
            ->middleware(static::getRouteMiddleware($pageGroup))
            ->withoutMiddleware(static::getWithoutRouteMiddleware($pageGroup))
            ->name((string) str($slug)->replace('/', '.'));
    }

    public static function getRouteName(?string $panel = null): string
    {
        $panel ??= LivewirePageGroup::getCurrentPageGroup()->getId();

        return (string) str(static::getSlug())
            ->replace('/', '.')
            ->prepend("livewirePageGroup.{$panel}.pages.");
    }

    public static function getSlug(): string
    {
        return static::$slug ?? (string) str(class_basename(static::class))
            ->kebab()
            ->slug();
    }

    /**
     * @return string | array<string>
     */
    public static function getRouteMiddleware(PageGroup $pageGroup): string|array
    {
        return [
            ...static::$routeMiddleware,
        ];
    }

    /**
     * @return string | array<string>
     */
    public static function getWithoutRouteMiddleware(PageGroup $pageGroup): string|array
    {
        return static::$withoutRouteMiddleware;
    }
}
